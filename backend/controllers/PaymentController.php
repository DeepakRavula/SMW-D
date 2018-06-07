<?php

namespace backend\controllers;

use Yii;
use common\models\Payment;
use backend\models\search\PaymentReportSearch;
use backend\models\search\PaymentSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Invoice;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;
use common\components\controllers\BaseController;
use backend\models\PaymentForm;

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
                'only' => [
                    'invoice-payment', 'credit-payment', 'update', 'delete', 'receive',
                    'validate-apply-credit'
                ],
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
                        'actions' => [
                            'index', 'update', 'view', 'delete', 'create', 'print', 'receive',
                            'invoice-payment', 'credit-payment', 'validate-apply-credit'
                        ],
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
        $currentDate = new \DateTime();
        $searchModel->startDate = $currentDate->format('M d, Y');
        $searchModel->endDate = $currentDate->format('M d, Y');
        $searchModel->dateRange = $searchModel->startDate.' - '.$searchModel->endDate;
        $request = Yii::$app->request;
        $paymentRequest = $request->get('PaymentSearch');
        if (!empty($paymentRequest['dateRange'])) {
            $searchModel->dateRange = $paymentRequest['dateRange'];
        }
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
        $model = new Payment(['scenario' => Payment::SCENARIO_CREATE]);

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
        $model->old = clone $model;
        $model->setScenario(Payment::SCENARIO_EDIT);
        $model->date = (new \DateTime($model->date))->format('d-m-Y');
        if ($model->isCreditUsed()) {
            $model->setScenario(Payment::SCENARIO_CREDIT_USED_EDIT);
        }
        $data = $this->renderAjax('/invoice/payment/_form', [
            'model' => $model,
        ]);
        $request = Yii::$app->request;
        if ($request->post()) {
            $model->load($request->post());
            $model->date = (new \DateTime($model->date))->format('Y-m-d H:i:s');
            if ($model->save()) {
                $model->invoice->save();
                $response = [
                    'status' => true,
                    'message' => 'Payment succesfully updated!'
                ];
            } else {
                $errors = ActiveForm::validate($model);
                $response = [
                    'status' => false,
                    'errors' => $errors
                ];
            }
        } else {
            $response = [
                'status' => true,
                'data' => $data,
            ];
        }
        return $response;
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
        $model->setScenario(Payment::SCENARIO_DELETE);
        if ($model->isCreditUsed()) {
            $model->setScenario(Payment::SCENARIO_CREDIT_USED_DELETE);
        }
        $modelInvoice = $model->invoice;
        if ($model->validate()) {
            $model->delete();
            $modelInvoice->save();
            $response = [
                'status' => true,
                'message' => 'Payment succesfully deleted!'
            ];
        } else {
            $errors = current($model->getErrors());
            $response = [
                'status' => false,
                'message' => current($errors)
            ];
        }
        return $response;
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
        $searchModel = new PaymentReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $this->layout = '/print';

        return $this->render('/report/payment/_print', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionInvoicePayment($id)
    {
        $paymentModel = new Payment(['scenario' => Payment::SCENARIO_CREATE]);
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
                    'canAlert' => $paymentModel->invoice->isPaid() && $paymentModel->invoice->isProformaInvoice()
                ];
            } else {
                $transaction->rollBack();
                $errors = ActiveForm::validate($paymentModel);
                return [
                    'status' => false,
                    'errors' => $errors,
                ];
            }
        }
    }

    public function actionValidateApplyCredit()
    {
        $paymentModel = new Payment(['scenario' => Payment::SCENARIO_APPLY_CREDIT]);
        $request = Yii::$app->request;
        $paymentModel->load($request->post());
        return ActiveForm::validate($paymentModel);
    }

    public function actionCreditPayment($id)
    {
        $model = Invoice::findOne(['id' => $id]);
        $paymentModel = new Payment(['scenario' => Payment::SCENARIO_APPLY_CREDIT]);
        $paymentModel->invoiceId = $model->id;
        $request = Yii::$app->request;
        if ($request->post()) {
            if ($paymentModel->load($request->post()) && $paymentModel->validate()) {
                $invoiceModel = Invoice::findOne(['id' => $paymentModel->sourceId]);
                $model->addPayment($invoiceModel, $paymentModel);
                $invoiceModel->save();
                $response = [
                    'status' => true,
                    'message' => 'Payment succesfully applied!'
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($paymentModel),
                    'message' => 'No credits available!',
                ];
            }
        } else {
             
            $creditDataProvider = $this->getAvailableCredit($model);
            $data = $this->renderAjax('/invoice/payment/payment-method/_apply-credit', [
                'invoice' => $model,
                'paymentModel' => $paymentModel,
                'creditDataProvider' => $creditDataProvider
            ]);
            $response = [
                'status' => true,
                'hasCredit' => $creditDataProvider->totalCount > 0,
                'data' => $data,
                'message' => $creditDataProvider->totalCount == 0 ? "No credits Available!" : "",
            ];
           
        }
        return $response;
    }

    public function getAvailableCredit($invoice)
    {
        $invoiceCredits = Invoice::find()
                ->notDeleted()
                ->invoiceCredit($invoice->user_id)
                ->andWhere(['NOT', ['invoice.id' => $invoice->id]])
                ->all();

        $results = [];
        if (!empty($invoiceCredits)) {
            foreach ($invoiceCredits as $invoiceCredit) {
                if ($invoiceCredit->isReversedInvoice()) {
                    $lastInvoicePayment = $invoiceCredit;
                } else {
                    $lastInvoicePayments = $invoiceCredit->payments;
                    $lastInvoicePayment = end($lastInvoicePayments);
                }
                $paymentDate = new \DateTime();
                if (!empty($lastInvoicePayment)) {
                    $paymentDate = \DateTime::createFromFormat('Y-m-d H:i:s', $lastInvoicePayment->date);
                }
                $amount = abs($invoiceCredit->balance);
                $results[] = [
                    'id' => $invoiceCredit->id,
                    'invoice_number' => $invoiceCredit->getInvoiceNumber(),
                    'date' => $paymentDate->format('d-m-Y'),
                    'amount' => $amount
                ];
            }
        }

        $creditDataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'sort' => [
                'attributes' => ['id', 'invoice_number', 'date', 'amount'],
            ],
        ]);
        return $creditDataProvider;
    }

    public function actionReceive()
    {
        $model = new PaymentForm();
        $paymentData = Yii::$app->request->get('PaymentForm');
        if ($paymentData) {
            $model->load(Yii::$app->request->get());
        }
        $request = Yii::$app->request;
        if ($request->post()) {
            
        } else {
            $data = $this->renderAjax('/receive-payment/_form', [
                'model' => $model
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }
}
