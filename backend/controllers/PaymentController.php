<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use backend\models\search\PaymentSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Invoice;
use common\models\PaymentMethod;
use yii\widgets\ActiveForm;
use yii\web\Response;
use common\models\CreditUsage;
use yii\filters\ContentNegotiator;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;

/**
 * PaymentsController implements the CRUD actions for Payments model.
 */
class PaymentController extends BaseController
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
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['invoice-payment', 'credit-payment', 'update', 'delete'],
                'formatParam' => '_format',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 'print', 'invoice-payment', 'credit-payment'],
                        'roles' => ['managePfi', 'manageInvoices'],
                    ],
                ],
            ],  
        ];
    }

    /**
     * Lists all Payments models.
     *
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
     *
     * @param string $id
     *
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
     *
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
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->date = (new \DateTime($model->date))->format('d-m-Y');
        if ($model->isCreditUsed()) {
            $model->setScenario(Payment::SCENARIO_CREDIT_USED);
        }
        $data = $this->renderAjax('/invoice/payment/_form', [
            'model' => $model,
        ]);
        $request = Yii::$app->request;
        if ($model->load($request->post())) {
            $model->date = (new \DateTime($model->date))->format('Y-m-d H:i:s');
            $model->save();
            $model->invoice->save();
            $response = [
                'status' => true
            ];
            return $response;
        } else {
            return [
                    'status' => true,
                    'data' => $data,
            ];
        }
    }

    /**
     * Deletes an existing Payments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model        = $this->findModel($id);
        $modelInvoice = $model->invoice;
        $model->delete();
        $modelInvoice->save();
        
        return [
            'status' => true,
        ];
    }

    /**
     * Finds the Payments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Payments the loaded model
     *
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

    public function actionPrint()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $this->layout = '/print';

        return $this->render('/report/payment/_print', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionInvoicePayment($id)
    {
        $paymentModel = new Payment();
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        $request = Yii::$app->request;
        if ($paymentModel->load($request->post())) {
            $paymentModel->date = (new \DateTime($paymentModel->date))->format('Y-m-d H:i:s');
            $paymentModel->invoiceId = $id;
            if ($paymentModel->save()) {
                $transaction->commit();
                return [
                    'status' => true,
                ];
            } else {
                $errors = ActiveForm::validate($paymentModel);
                return [
                'status' => false,
                'errors' => $errors,
            ];
            }
        }
    }

    public function actionCreditPayment($id)
    {
        $model = Invoice::findOne(['id' => $id]);
        $paymentModel = new Payment();
        $request = Yii::$app->request;
        if ($paymentModel->load($request->post())) {
            $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
            $paymentModel->reference = $paymentModel->sourceId;
            $paymentModel->invoiceId = $model->id;
            if ($paymentModel->validate()) {
                $paymentModel->save();

                $creditPaymentId = $paymentModel->id;
                $paymentModel->id = null;
                $paymentModel->isNewRecord = true;
                $paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
                $paymentModel->invoiceId = $paymentModel->sourceId;
                $paymentModel->reference = $model->id;
                $paymentModel->save();

                $debitPaymentId = $paymentModel->id;
                $creditUsageModel = new CreditUsage();
                $creditUsageModel->credit_payment_id = $creditPaymentId;
                $creditUsageModel->debit_payment_id = $debitPaymentId;
                $creditUsageModel->save();

                $invoiceModel = Invoice::findOne(['id' => $paymentModel->sourceId]);
                $invoiceModel->balance = $invoiceModel->balance + abs($paymentModel->amount);
                $invoiceModel->save();
                $response = [
                    'status' => true,
                ];
            } else {
                $paymentModel = ActiveForm::validate($paymentModel);
                $response = [
                    'status' => false,
                    'errors' => $paymentModel,
                ];
            }
        } else {
            $data = $this->renderAjax('/invoice/payment/payment-method/_apply-credit', [
                'invoice' => $model
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }
}
