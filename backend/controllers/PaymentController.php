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
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use common\models\User;
use backend\models\search\PaymentFormLessonSearch;
use common\models\LessonPayment;
use common\models\InvoicePayment;

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
                    'validate-apply-credit', 'validate-receive','update-payment'
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
                            'invoice-payment', 'credit-payment', 'validate-apply-credit',
                            'validate-receive','update-payment'
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

    public function getCustomerCreditInvoices($customerId)
    {
        return Invoice::find()
            ->notDeleted()
            ->invoiceCredit($customerId)
            ->all();
    }

    public function getAvailableCredit($customerId = null)
    {
        $invoiceCredits = $this->getCustomerCreditInvoices($customerId);
        $results = [];
        $amount = 0;
        $paymentCredits = $this->getCustomerPayments($customerId);
        
        if ($invoiceCredits) {
            foreach ($invoiceCredits as $invoiceCredit) {
                $results[] = [
                    'id' => $invoiceCredit->id,
                    'type' => 'Invoice Credit',
                    'reference' => $invoiceCredit->getInvoiceNumber(),
                    'amount' => abs($invoiceCredit->balance)
                ];
            }
        }

        if ($paymentCredits) {
            foreach ($paymentCredits as $paymentCredit) {
                if ($paymentCredit->hasCredit()) {
                    $results[] = [
                        'id' => $paymentCredit->id,
                        'type' => 'Payment Credit',
                        'reference' => $paymentCredit->reference,
                        'amount' => $paymentCredit->creditAmount
                    ];
                }
            }
        }
        
        $creditDataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'sort' => [
                'attributes' => ['type', 'reference', 'amount']
            ]
        ]);
        return $creditDataProvider;
    }

    public function getCustomerPayments($customerId)
    {
        return Payment::find()
            ->notDeleted()
            ->exceptAutoPayments()
            ->customer($customerId)
            ->all();
    }

    public function actionReceive()
    {
        $request = Yii::$app->request;
        $searchModel = new PaymentFormLessonSearch();
        $searchModel->showCheckBox = true;
        $model = new PaymentForm();
        $currentDate = new \DateTime();
        $model->date = $currentDate->format('M d, Y');
        if (!$request->post()) {
            $searchModel->fromDate = $currentDate->format('M 1, Y');
            $searchModel->toDate = $currentDate->format('M t, Y'); 
            $searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
        }
        $searchModel->load(Yii::$app->request->get());
	    $model->userId = $searchModel->userId;
        $lessonsQuery = $searchModel->search(Yii::$app->request->queryParams);
        $lessonsQuery->orderBy(['lesson.id' => SORT_ASC]);
        $model->load(Yii::$app->request->get());
        $lessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $lessonsQuery,
            'pagination' => false
        ]);
        $invoicesQuery = Invoice::find();
        if ($model->invoiceIds) {
            $invoicesQuery->andWhere(['id' => $model->invoiceIds]);
        } else {
            if (!$searchModel->userId) {
                $searchModel->userId = null;
            }
            $invoicesQuery->notDeleted()
                ->invoice()
                ->customer($searchModel->userId)
                ->unpaid();
        }
        $invoicesQuery->orderBy(['invoice.id' => SORT_ASC]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoicesQuery,
            'pagination' => false 
        ]);
        $creditDataProvider = $this->getAvailableCredit($searchModel->userId);
        if ($request->post()) {
            $model->load($request->post());
            $payment = new Payment();
            $payment->amount = $model->amount;
            $payment->user_id = $searchModel->userId;
            $payment->payment_method_id = $model->payment_method_id;
            $payment->date = (new \DateTime($model->date))->format('Y-m-d H:i:s');
            $payment->save();
            $model->paymentId = $payment->id;
            $model->lessonIds = $searchModel->lessonIds;
            $model->save();
            $response = [
                'status' => true,
                'message' => 'Payment added succesfully'
            ];
        } else {
            $data = $this->renderAjax('/receive-payment/_form', [
                'model' => $model,
                'creditDataProvider' => $creditDataProvider,
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                'searchModel' => $searchModel
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionValidateReceive()
    {
        $request = Yii::$app->request;
        $model = new PaymentForm();
        $model->load($request->post());
        if (!$model->amount) {
            $model->amount = 0.0;
        }
        return ActiveForm::validate($model);
    }

    public function actionUpdatePayment($id)
    {
        $model = $this->findModel($id);
	    $lessonPayment = Lesson::find()
		    ->joinWith(['lessonPayments' => function ($query) use ($id) {
                $query->andWhere(['paymentId' => $id]);
            }]);
	    $lessonDataProvider = new ActiveDataProvider([
            'query' => $lessonPayment,
            'pagination' => false
        ]);
	    
        $invoicePayment = Invoice::find()
            ->notDeleted()
            ->joinWith(['invoicePayments' => function ($query) use ($id) {
                $query->andWhere(['payment_id' => $id]);
            }]);
	    
	    $invoiceDataProvider = new ActiveDataProvider([
            'query' => $invoicePayment,
            'pagination' => false
        ]);
        if (Yii::$app->request->post()) {
           
        } else {
            $data = $this->renderAjax('_form', [
                'model' => $model,
                'lessonDataProvider' => $lessonDataProvider,
                'invoiceDataProvider' => $invoiceDataProvider
            ]);
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }
}
