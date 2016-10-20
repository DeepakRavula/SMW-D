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
use common\models\InvoiceLineItem;

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
		$paymentModel = new Payment();
		$db = \Yii::$app->db;
		$transaction = $db->beginTransaction();
		$request = Yii::$app->request;
		if ($paymentModel->load($request->post())) {
			$paymentModel->invoiceId = $id;
			$paymentModel->save();
			$transaction->commit();
			Yii::$app->session->setFlash('alert',
				[
				'options' => ['class' => 'alert-success'],
				'body' => 'Payment has been recorded successfully'
			]);
			return $this->redirect(['invoice/view', 'id' => $id, '#' => 'payment']);
		}
	}

	public function actionCreditPayment($id) {
		$model = Invoice::findOne(['id' => $id]);
		$paymentModel = new Payment();
		$paymentModel->setScenario('apply-credit');
		$response = \Yii::$app->response;
		$response->format = Response::FORMAT_JSON;
		$request = Yii::$app->request;
		if ($paymentModel->load($request->post())) {
			$paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
			$paymentModel->reference		 = $paymentModel->sourceId;
			$paymentModel->invoiceId = $model->id;
			if ($paymentModel->validate()) {
				$paymentModel->save();

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

				$invoiceModel			 = Invoice::findOne(['id' => $paymentModel->sourceId]);
				$invoiceModel->balance	 = $invoiceModel->balance + abs($paymentModel->amount);
				$invoiceModel->save();
				$response = [
					'status' => true,
				];
			} else {
				$paymentModel = ActiveForm::validate($paymentModel);
                $response = [
					'status' => false,
					'errors' => $paymentModel
				];
			}
			return $response;
		}
	}

	public function actionEdit($id)
    {
        $request          = Yii::$app->request;
        $response         = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $post             = $request->post();
        if ($request->post('hasEditable')) {
            $paymentIndex = $request->post('editableIndex');
            $model        = Payment::findOne(['id' => $id]);
            if (!empty($post['Payment'][$paymentIndex]['amount'])) {
                $newAmount = $post['Payment'][$paymentIndex]['amount'];
                if ($model->isOtherPayments()) {
                    $response = Yii::$app->runAction('payment/edit-other-payments',
                        ['model' => $model, 'newAmount' => $newAmount]);
                }
                if ($model->isAccountEntry()) {
                    $response = Yii::$app->runAction('payment/edit-account-entry',
                        ['model' => $model, 'newAmount' => $newAmount]);
                }
                if ($model->isCreditApplied()) {
                    $response = Yii::$app->runAction('payment/edit-credit-applied',
                        ['model' => $model, 'newAmount' => $newAmount]);
                }

                if ($model->isCreditUsed()) {
                    $response = Yii::$app->runAction('payment/edit-credit-used',
                        ['model' => $model, 'newAmount' => $newAmount]);
                }

                return $response;
            }
        }
    }

    public function actionEditOtherPayments($model, $newAmount)
    {
        $model->amount = $newAmount;
        $model->save();

        $result = [
            'output' => $newAmount,
            'message' => ''
        ];

        return $result;
    }

    public function actionEditAccountEntry($model, $newAmount)
    {
        $model->amount            = $newAmount;
        $model->save();
        $lineItem                 = InvoiceLineItem::findOne(['invoice_id' => $model->invoice->id]);
        $lineItem->amount         = $model->amount;
        $model->invoice->subTotal = $lineItem->amount;
        $model->invoice->total    = $model->invoice->subTotal + $model->invoice->tax;
        $lineItem->save();

        $result = [
            'output' => $newAmount,
            'message' => ''
        ];

        return $result;
    }

    public function actionEditCreditApplied($model, $newAmount)
    {
        $model->setScenario(Payment::SCENARIO_CREDIT_APPLIED);
        $model->lastAmount = $model->amount;
        $model->amount     = $newAmount;
        $model->differnce  = $model->amount - $model->lastAmount;
        if ($model->validate()) {
            $model->save();

            $result = [
                'output' => $newAmount,
                'message' => ''
            ];
        } else {
            $errors = ActiveForm::validate($model);

            $result = [
                'output' => false,
                'message' => $errors['payment-amount'],
            ];
        }

        return $result;
    }

    public function actionEditCreditUsed($model, $newAmount)
    {
        $model->setScenario(Payment::SCENARIO_CREDIT_USED);
        $model->lastAmount = $model->amount;
        $model->amount     = $newAmount;
        $model->differnce  = $model->amount - $model->lastAmount;
        if ($model->validate()) {
            $model->save();

            $result = [
                'output' => $newAmount,
                'message' => ''
            ];
        } else {
            $errors = ActiveForm::validate($model);

            $result = [
                'output' => false,
                'message' => $errors['payment-amount'],
            ];
        }

        return $result;
    }
}