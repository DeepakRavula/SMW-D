<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use backend\models\search\InvoiceSearch;
use common\models\User;
use common\models\Payment;
use common\models\Lesson;
use common\models\PaymentMethod;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ItemType;
use common\models\CreditUsage;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends Controller {

	public function behaviors() {
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	/**
	 * Lists all Invoice models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new InvoiceSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
					'searchModel' => $searchModel,
					'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single Invoice model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {
		$model = $this->findModel($id);
		$invoiceLineItems = InvoiceLineItem::find()->where(['invoice_id' => $id]);
		$invoiceLineItemsDataProvider = new ActiveDataProvider([
			'query' => $invoiceLineItems,
		]);

		$invoicePayments = Payment::find()
				->joinWith(['invoicePayment ip' => function($query) use($model){
					$query->where(['ip.invoice_id' => $model->id]);	
				}])
				->where(['user_id' => $model->user_id]);
		
		$invoicePaymentsDataProvider = new ActiveDataProvider([
			'query' => $invoicePayments,
		]);

		$paymentModel = new Payment();
		if ($paymentModel->load(Yii::$app->request->post())) {
				$paymentMethodId = $paymentModel->payment_method_id; 
				$paymentModel->user_id = $model->user_id;
				$currentDate = new \DateTime();
				$paymentModel->date = $currentDate->format('Y-m-d H:i:s');
				$paymentModel->amount = $paymentModel->amount;
				if((int) $paymentModel->payment_method_id === PaymentMethod::TYPE_CREDIT){
					$paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
				}
				$paymentModel->invoiceId = $model->id;
				$paymentModel->save();
			
			if($model->total < $paymentModel->amount){
				$model->balance =  $model->total - $paymentModel->amount;
				$model->save();
			}
			
			if($paymentModel->sourceType == 'invoice'){
				$invoiceModel = $this->findModel($paymentModel->sourceId);
				$invoiceModel->balance = $invoiceModel->balance + $paymentModel->amount;
				$invoiceModel->save();
			}
			$creditPaymentId = $paymentModel->id;
			if((int) $paymentMethodId === PaymentMethod::TYPE_CREDIT){
				$paymentModel->id = null;
				$paymentModel->isNewRecord = true;	
				$paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
				$paymentModel->amount = -abs($paymentModel->amount);
				if($paymentModel->sourceType == 'account_entry'){
					$paymentModel->invoiceId = Payment::TYPE_OPENING_BALANCE_CREDIT;
				}else{
					$paymentModel->invoiceId = $paymentModel->sourceId;
				}
				$paymentModel->save();
				$debitPaymentId = $paymentModel->id;
				$creditUsageModel = new CreditUsage();
				$creditUsageModel->credit_payment_id = $creditPaymentId;  
				$creditUsageModel->debit_payment_id = $debitPaymentId;
				$creditUsageModel->save();	
			}

			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Payment has been recorded successfully'
			]);
			return $this->redirect(['view', 'id' => $model->id]);
		}
		
		$invoiceLineItemModel = new InvoiceLineItem();
		if ($invoiceLineItemModel->load(Yii::$app->request->post())) {
			$invoiceLineItemModel->item_id = Invoice::ITEM_TYPE_MISC; 
			$invoiceLineItemModel->invoice_id = $model->id; 
			$invoiceLineItemModel->item_type_id = ItemType::TYPE_MISC;
			$invoiceLineItemModel->save();

			$model = $this->findModel($id);
			$model->subTotal = $model->total + $invoiceLineItemModel->amount;
			$model->total = $model->subTotal;
			$model->save();

			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Misc has been added successfully'
			]);
			return $this->redirect(['view', 'id' => $model->id]);
		}
		return $this->render('view', [
					'model' => $model,
					'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
					'invoicePayments' => $invoicePaymentsDataProvider,
		]);
	}

	/**
	 * Creates a new Invoice model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		$invoice = new Invoice();
		$request = Yii::$app->request;
		$invoiceRequest = $request->get('Invoice');
		$invoice->type = $invoiceRequest['type'];
		$unInvoicedLessonsDataProvider = null;
		$location_id = Yii::$app->session->get('location_id');

		if (isset($invoiceRequest['customer_id'])) {
			$customer = User::findOne(['id' => $invoiceRequest['customer_id']]);

			if (empty($customer)) {
				throw new NotFoundHttpException('The requested page does not exist.');
			}

			$currentDate = new \DateTime();
			$invoice->customer_id = $customer->id;
			$query = Lesson::find()->alias('l')
					->location($location_id)
					->student($customer->id)
					->unInvoiced();
				if((int) $invoice->type === Invoice::TYPE_PRO_FORMA_INVOICE){
					$query->scheduled();
				}else{
					$query->completed();
				}
			
			$unInvoicedLessonsDataProvider = new ActiveDataProvider([
				'query' => $query,
			]);
		}

		$post = $request->post();
		if (!empty($post['selection']) && is_array($post['selection'])) {
			$invoice->type = $invoiceRequest['type'];
			$lastInvoice = Invoice::lastInvoice($location_id);

			if (empty($lastInvoice)) {
				$invoiceNumber = 1;
			} else {
				$invoiceNumber = $lastInvoice->invoice_number + 1;
			}
			$invoice->user_id = $customer->id;
			$invoice->invoice_number = $invoiceNumber;
			$invoice->date = (new \DateTime())->format('Y-m-d');
			$invoice->notes = $post['Invoice']['notes'];
			$invoice->internal_notes = $post['Invoice']['internal_notes'];
			$invoice->save();
			
			$subTotal = 0;
			$taxAmount = 0;
			foreach ($post['selection'] as $selection) {
				$lesson = Lesson::findOne(['id' => $selection]);
				$actualLessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $lesson->date);
				$lessonDate = $actualLessonDate->format('Y-m-d');
				$invoiceLineItem = new InvoiceLineItem();
				$invoiceLineItem->invoice_id = $invoice->id;
				$invoiceLineItem->item_id = $lesson->id;
            	$invoiceLineItem->item_type_id = ItemType::TYPE_LESSON;
				$description = $lesson->enrolment->program->name . ' for ' . $lesson->enrolment->student->fullName . ' with ' . $lesson->teacher->publicIdentity;
    	        $invoiceLineItem->description = $description;
				$time = explode(':', $lesson->enrolment->duration);
				$invoiceLineItem->unit = (($time[0] * 60) + ($time[1])) / 60;
				$invoiceLineItem->amount = $lesson->enrolment->program->rate * $invoiceLineItem->unit;
				$invoiceLineItem->save();
				$subTotal += $invoiceLineItem->amount;
			}
			$invoice = Invoice::findOne(['id' => $invoice->id]);
			$invoice->subTotal = $subTotal;
			$totalAmount = $subTotal + $taxAmount;
			$invoice->tax = $taxAmount;
			$invoice->total = $totalAmount;
			$invoice->save();

			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Invoice has been created successfully'
			]);

			return $this->redirect(['view', 'id' => $invoice->id]);
		} else {
			return $this->render('create', [
				'model' => $invoice,
				'unInvoicedLessonsDataProvider' => $unInvoicedLessonsDataProvider,
			]);
		}
	}

	/**
	 * Updates an existing Invoice model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('update', [
						'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Invoice model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	/**
	 * Finds the Invoice model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Invoice the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id) {
		$session = Yii::$app->session;
		$locationId = $session->get('location_id');
		$model = Invoice::find()->location($locationId)
						->where(['invoice.id' => $id])->one();
		if ($model !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

	public function actionAddInvoice() {
		$model = new User();

		if (isset(Yii::$app->request->queryParams['User'])) {
			$model->customer = Yii::$app->request->queryParams['User']["customer"];
		}

		return $this->render('create', [
					'model' => $model,
		]);
	}

	public function actionPrint($id) {

		$model = $this->findModel($id);
		$invoiceLineItems = InvoiceLineItem::find()->where(['invoice_id' => $id]);
		$invoiceLineItemsDataProvider = new ActiveDataProvider([
			'query' => $invoiceLineItems,
		]);
		$this->layout = "/print-invoice";
		return $this->render('_print', [
					'model' => $model,
					'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider
		]);
	}

	public function actionSendMail($id) {
		$model = $this->findModel($id);
		$invoiceLineItems = InvoiceLineItem::find()->where(['invoice_id' => $id]);
		$invoiceLineItemsDataProvider = new ActiveDataProvider([
			'query' => $invoiceLineItems,
		]);
		$subject = 'Invoice from ' . Yii::$app->name;

		Yii::$app->mailer->compose('generateInvoice', [
			'model' => $model,
			'toName' => $model->lineItems[0]->lesson->enrolment->student->customer->publicIdentity,
			'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
		])
			->setFrom(\Yii::$app->params['robotEmail'])
			->setTo($model->lineItems[0]->lesson->enrolment->student->customer->email)
			->setSubject($subject)
			->send();
		return $this->redirect(['view', 'id' => $model->id]);
	}

}
				