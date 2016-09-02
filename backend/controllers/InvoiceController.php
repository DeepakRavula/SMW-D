<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use backend\models\search\InvoiceSearch;
use backend\models\search\LessonSearch;
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
use common\models\PaymentCheque;
use common\models\TaxCode;
use common\models\Location;
use common\models\TaxStatus;
use common\models\Address;
use common\models\UserAddress;
use common\models\PhoneNumber;
use yii\helpers\Json;

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
				if((int) $paymentModel->payment_method_id === PaymentMethod::TYPE_APPLY_CREDIT){
					$paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
				}
				$paymentModel->invoiceId = $model->id;
				$paymentModel->save();
				if((int) $paymentModel->payment_method_id === PaymentMethod::TYPE_CHEQUE){
					$chequeModel = new PaymentCheque();
					if ($chequeModel->load(Yii::$app->request->post())) {
						$chequeModel->payment_id = $paymentModel->id;
						$chequeDate = \DateTime::createFromFormat('d-m-Y',$chequeModel->date);
						$chequeModel->date = $chequeDate->format('Y-m-d H:i:s');
						$chequeModel->save();
					}
				}
				
				if((int) $model->type === Invoice::TYPE_INVOICE){	
					if($model->total < $paymentModel->amount){
						$model->balance =  $model->total - $paymentModel->amount;
						$model->save();
					}else{
						$model->balance =  $model->invoiceBalance;
						$model->save();	
					}
				}
			
			$creditPaymentId = $paymentModel->id;
			if((int) $paymentMethodId === PaymentMethod::TYPE_APPLY_CREDIT){
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

				if($paymentModel->sourceType == 'invoice'){
					$invoiceModel = $this->findModel($paymentModel->sourceId);
					$invoiceModel->balance = $invoiceModel->balance + abs($paymentModel->amount);
					$invoiceModel->save();
				}
			}

			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Payment has been recorded successfully'
			]);
			return $this->redirect(['view', 'id' => $model->id, '#' => 'payment']);
		}
		
		$invoiceLineItemModel = new InvoiceLineItem();
		if ($invoiceLineItemModel->load(Yii::$app->request->post())) {
			$invoiceLineItemModel->item_id = Invoice::ITEM_TYPE_MISC; 
			$invoiceLineItemModel->invoice_id = $model->id; 
			$invoiceLineItemModel->item_type_id = ItemType::TYPE_MISC;
			$taxStatus = TaxStatus::findOne(['id' => $invoiceLineItemModel->tax_status]);
			$invoiceLineItemModel->tax_status = $taxStatus->name;
			$invoiceLineItemModel->save();

			$model = $this->findModel($id);
			$model->subTotal += $invoiceLineItemModel->amount;
			$model->tax += $invoiceLineItemModel->tax_rate;
			$model->total = $model->subTotal + $model->tax;
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

	public function actionComputeTax() {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $locationId = Yii::$app->session->get('location_id');
		$locationModel = Location::findOne(['id' => $locationId]);
        $today = (new \DateTime())->format('Y-m-d H:i:s');
        $data = Yii::$app->request->rawBody;
        $data = Json::decode($data, true);
        $taxCode = TaxCode::find()->where(['<=', 'start_date', $today])->andWhere(['province_id'=> $locationModel->province_id])
			->orderBy('start_date DESC')
			->one();
        $rate = $data['amount'] * $taxCode->rate/100;
        return [
			'tax_type' => $taxCode->taxType->name,
			'code' => $taxCode->code,
			'rate' => $rate,
			'tax_status' => $data['taxStatus']
		];
    }

	/**
	 * Creates a new Invoice model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate() {
		$searchModel = new LessonSearch();
		$params = Yii::$app->request->queryParams;
		if( ! empty($params['Invoice']['customer_id'])){
			$params['LessonSearch']['customerId'] = $params['Invoice']['customer_id']; 
		}
		if( ! empty($params['Invoice']['type'])){
			$params['LessonSearch']['invoiceType'] = $params['Invoice']['type']; 
		}
		$dataProvider = $searchModel->search($params);	
		$invoice = new Invoice();
		$request = Yii::$app->request;
		$invoiceRequest = $request->get('Invoice');
		$invoice->type = $invoiceRequest['type'];
		$location_id = Yii::$app->session->get('location_id');
		if (isset($invoiceRequest['customer_id'])) {
			$customer = User::findOne(['id' => $invoiceRequest['customer_id']]);

			if (empty($customer)) {
				throw new NotFoundHttpException('The requested page does not exist.');
			}
            
			$currentDate = new \DateTime();
			$invoice->customer_id = $customer->id;
			$searchModel->customerId = $customer->id; 
			$searchModel->invoiceType = $invoice->type;	
		}
		$post = $request->post();
		if (( ! empty($post['selection'])) && is_array($post['selection']) && (! empty($customer->id))) {
			$invoice->type = $invoiceRequest['type'];
			$lastInvoice = Invoice::lastInvoice($location_id);
			switch ($invoice->type) {
                case Invoice::TYPE_PRO_FORMA_INVOICE:
                    $invoiceNumber = 0;
                    break;
                case Invoice::TYPE_INVOICE:
                    if (empty($lastInvoice)) {
                        $invoiceNumber = 1;
                    } else {
                        $invoiceNumber = $lastInvoice->invoice_number + 1;
                    }
                    break;
            }            
			
			$invoice->user_id = $customer->id;
			$invoice->invoice_number = $invoiceNumber;
			$invoice->date = (new \DateTime())->format('Y-m-d');
			$invoice->status = Invoice::STATUS_OWING;
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
				$taxStatus = TaxStatus::findOne(['id' => TaxStatus::STATUS_NO_TAX]);
				$invoiceLineItem->tax_type = $taxStatus->taxTypeTaxStatusAssoc->taxType->name;
				$invoiceLineItem->tax_rate = 0.0;
				$invoiceLineItem->tax_code = $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->code;
				$invoiceLineItem->tax_status = $taxStatus->name;
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
            
            $invoiceType = (int) $invoice->type === Invoice::TYPE_INVOICE ? 'Invoice' : 'Pro-forma invoice';
			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => $invoiceType . ' ' . 'has been created successfully'
			]);

			return $this->redirect(['view', 'id' => $invoice->id]);
		} else {
			return $this->render('create', [
				'model' => $invoice,
				'dataProvider' => $dataProvider,
                'customer' => (empty($customer)) ? [new User] : $customer,
				'searchModel' => $searchModel,
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
			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => 'Invoice has been updated successfully'
			]);
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
		if(! empty($model->user->email)){
			Yii::$app->mailer->compose('generateInvoice', [
				'model' => $model,
				'toName' => $model->user->publicIdentity,
				'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
			])
				->setFrom(\Yii::$app->params['robotEmail'])
				->setTo($model->user->email)
				->setSubject($subject)
				->send();

			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-success'],
				'body' => ' Mail has been send successfully'
			]);
		}else{
			Yii::$app->session->setFlash('alert', [
				'options' => ['class' => 'alert-danger'],
				'body' => 'The customer doesn\'t have email id' 
			]);	
		}
		return $this->redirect(['view', 'id' => $model->id]);
	}

}
				