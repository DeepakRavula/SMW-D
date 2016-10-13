<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use backend\models\search\PaymentSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Invoice;
use common\models\PaymentMethod;
use yii\widgets\ActiveForm;
use yii\web\Response;
use common\models\CreditUsage;

/**
 * PaymentsController implements the CRUD actions for Payments model.
 */
class PaymentController extends Controller
{
    public function behaviors()
    {
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
     * Lists all Payments models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('index', [
					'searchModel' => $searchModel,
					'dataProvider' => $dataProvider,
		]);
    }

    /**
     * Displays a single Payments model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Payments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Payment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Payments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
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
     * Deletes an existing Payments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Payments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Payments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	public function actionPrint() {
		$paymentDataProvider = new ActiveDataProvider([
			'query' => Payment::find(),
			'pagination' => false,
		]);
		$this->layout = "/print";
		return $this->render('_print', [
			'paymentDataProvider' => $paymentDataProvider
		]);
	}

	public function actionInvoicePayment($id) {
		$model = Invoice::findOne(['id' => $id]);
		$paymentModel = new Payment();
		$db = \Yii::$app->db;
		$transaction = $db->beginTransaction();
		if ($paymentModel->load(Yii::$app->request->post())) {
			$paymentMethodId		 = $paymentModel->payment_method_id;
			$paymentModel->user_id	 = $model->user_id;
			$paymentModel->date		 = (new \DateTime())->format('Y-m-d H:i:s');
			$paymentModel->invoiceId = $model->id;
			$paymentModel->save();
			$model->balance = $this->getInvoiceBalance($model, $paymentModel);
			$model->save();			
			$transaction->commit();
			Yii::$app->session->setFlash('alert',
				[
				'options' => ['class' => 'alert-success'],
				'body' => 'Payment has been recorded successfully'
			]);
			return $this->redirect(['invoice/view', 'id' => $model->id, '#' => 'payment']);
		}
	}

	public function getInvoiceBalance($model, $paymentModel) {
		if ($model->total < $paymentModel->amount) {
			$invoiceBalance = $model->total - $paymentModel->amount;
		} else {
			$invoiceBalance = $model->invoiceBalance;
		}
		return $invoiceBalance;
	}
	
	public function actionCreditPayment($id) {
		$model = Invoice::findOne(['id' => $id]);
		$paymentModel = new Payment();
		$paymentModel->setScenario('apply-credit');
		$response = \Yii::$app->response;
		$response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		if ($paymentModel->load(Yii::$app->request->post())) {
			$paymentModel->user_id	 = $model->user_id;
			$paymentModel->date		 = (new \DateTime())->format('Y-m-d H:i:s');
			$paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
			$paymentModel->reference		 = $paymentModel->sourceId;
			$paymentModel->invoiceId = $model->id;
			if ($request->isAjax && $paymentModel->validate()) {
				$paymentModel->save();
				
				$model->balance = $this->getInvoiceBalance($model, $paymentModel);
				$model->save();

				$creditPaymentId = $paymentModel->id;
				$paymentModel->id				 = null;
				$paymentModel->isNewRecord		 = true;
				$paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
				$paymentModel->invoiceId		 = $paymentModel->sourceId;
				$paymentModel->reference		 = $model->id;
				$paymentModel->save();
				$debitPaymentId						 = $paymentModel->id;
				$creditUsageModel					 = new CreditUsage();
				$creditUsageModel->credit_payment_id = $creditPaymentId;
				$creditUsageModel->debit_payment_id	 = $debitPaymentId;
				$creditUsageModel->save();

				if ($paymentModel->sourceType != 'pro_forma_invoice') {
					$invoiceModel			 = Invoice::findOne(['id' => $paymentModel->sourceId]);
					$invoiceModel->balance	 = $invoiceModel->balance + abs($paymentModel->amount);
					$invoiceModel->save();
				}
				return [
					'status' => 'true',
					'message' => 'Misc has been added successfully',
				];
			} else {
				$paymentModel = ActiveForm::validate($paymentModel);
                return [
					'status' => 'false',
					'errors' => $paymentModel
				];
			}

			Yii::$app->session->setFlash('alert',
				[
				'options' => ['class' => 'alert-success'],
				'body' => 'Payment has been recorded successfully'
			]);
			return $this->redirect(['invoice/view', 'id' => $model->id, '#' => 'payment']);
		}
	}
}
