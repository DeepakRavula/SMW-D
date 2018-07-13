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
use backend\models\PaymentEditForm;
use common\models\Lesson;
use common\models\InvoicePayment;
use common\models\LessonPayment;
use yii\data\ActiveDataProvider;
use backend\models\search\PaymentFormLessonSearch;
use backend\models\search\PaymentFormGroupLessonSearch;
use common\models\Receipt;
use common\models\PaymentReceipt;
use common\models\Location;


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
                    'validate-apply-credit', 'validate-receive', 'update-payment', 'view',
                    'validate-update'
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
                            'validate-receive', 'update-payment', 'validate-update'
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
    public function actionView()
    {
        if (Yii::$app->request->get('PaymentEditForm')) {
            $request = Yii::$app->request->get('PaymentEditForm');
            if (!empty($request['paymentId'])) {
                $paymentId = $request['paymentId'];
            }
            if (!empty($request['invoicePaymentId'])) {
                $invoicePaymentId = $request['invoicePaymentId'];
                $invoicePayment = InvoicePayment::findOne($invoicePaymentId);
                $paymentId = $invoicePayment->payment_id;
            }
            if (!empty($request['lessonPaymentId'])) {
                $lessonPaymentId = $request['lessonPaymentId'];
                $lessonPayment = LessonPayment::findOne($lessonPaymentId);
                $paymentId = $lessonPayment->paymentId;
            }
        }
        $model = $this->findModel($paymentId);
        $lessonPayment = Lesson::find()
            ->privateLessons()
            ->notDeleted()
		    ->joinWith(['lessonPayments' => function ($query) use ($paymentId) {
                $query->andWhere(['paymentId' => $paymentId]);
            }]);
	    $lessonDataProvider = new ActiveDataProvider([
            'query' => $lessonPayment,
            'pagination' => false
        ]);

        $groupLessonPayment = Lesson::find()
            ->groupLessons()
            ->notDeleted()
		    ->joinWith(['lessonPayments' => function ($query) use ($paymentId) {
                $query->andWhere(['paymentId' => $paymentId]);
            }]);
	    $groupLessonDataProvider = new ActiveDataProvider([
            'query' => $groupLessonPayment,
            'pagination' => false
        ]);
	    
        $invoicePayment = Invoice::find()
            ->notDeleted()
            ->joinWith(['invoicePayments' => function ($query) use ($paymentId) {
                $query->andWhere(['payment_id' => $paymentId]);
            }]);
	    
	    $invoiceDataProvider = new ActiveDataProvider([
            'query' => $invoicePayment,
            'pagination' => false
        ]);
        if (!Yii::$app->request->isPost) {
            $data = $this->renderAjax('view', [
                'model' => $model,
                'canEdit' => false,
                'lessonDataProvider' => $lessonDataProvider,
                'groupLessonDataProvider' => $groupLessonDataProvider,
                'invoiceDataProvider' => $invoiceDataProvider
            ]);
            return [
                'status' => true,
                'data' => $data
            ];
        }
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
        $payment = $this->findModel($id);
        $model = new PaymentEditForm();
        $model->paymentId = $payment->id;
        $model->userId = $payment->user_id;
        $model->amount = $payment->amount;
        $lessonPayment = Lesson::find()
            ->privateLessons()
            ->notDeleted()
		    ->joinWith(['lessonPayments' => function ($query) use ($id) {
                $query->andWhere(['paymentId' => $id]);
            }])
            ->orderBy(['lesson.id' => SORT_ASC]);
	    $lessonDataProvider = new ActiveDataProvider([
            'query' => $lessonPayment,
            'pagination' => false
        ]);

        $groupLessonPayment = Lesson::find()
            ->groupLessons()
            ->notDeleted()
		    ->joinWith(['lessonPayments' => function ($query) use ($id) {
                $query->andWhere(['paymentId' => $id]);
            }])
            ->orderBy(['lesson.id' => SORT_ASC]);
	    $groupLessonDataProvider = new ActiveDataProvider([
            'query' => $groupLessonPayment,
            'pagination' => false
        ]);
	    
        $invoicePayment = Invoice::find()
            ->notDeleted()
            ->joinWith(['invoicePayments' => function ($query) use ($id) {
                $query->andWhere(['payment_id' => $id]);
            }])
            ->orderBy(['invoice.id' => SORT_ASC]);
	    
	    $invoiceDataProvider = new ActiveDataProvider([
            'query' => $invoicePayment,
            'pagination' => false
        ]);
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->get());
            $payment->load(Yii::$app->request->post());
            $model->load(Yii::$app->request->post());
            $payment->amount = $model->amount;
            $payment->date = (new \DateTime($payment->date))->format('Y-m-d H:i:s');
            if (round($payment->amount, 2) > 0.00) {
                $payment->save();
                $model->save();
            } else {
                $payment->delete();
            }
            $response = [
                'status' => true
            ];
        } else {
            if ($payment->isAutoPayments()) {
                $response = [
                    'status' => false,
                    'message' => "System generated payments can't be deleted!"
                ];
            } else {
                $data = $this->renderAjax('_form', [
                    'model' => $model,
                    'paymentModel' => $payment,
                    'canEdit' => true,
                    'groupLessonDataProvider' => $groupLessonDataProvider,
                    'lessonDataProvider' => $lessonDataProvider,
                    'invoiceDataProvider' => $invoiceDataProvider
                ]);
                $response = [
                    'status' => true,
                    'data' => $data
                ];
            }
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
            ->orderBy(['payment.id' => SORT_ASC])
            ->all();
    }

    public function actionReceive()
    {
        $request = Yii::$app->request;
        $groupLessonSearchModel = new PaymentFormGroupLessonSearch();
        $groupLessonSearchModel->showCheckBox = true;
        $searchModel = new PaymentFormLessonSearch();
        $searchModel->showCheckBox = true;
        $model = new PaymentForm();
        $currentDate = new \DateTime();
        $model->date = $currentDate->format('M d, Y');
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $receiptLessonIds = [];
        $receiptInvoiceIds = [];
        $receiptPaymentIds = [];
        if (!$request->post()) {
            $groupLessonSearchModel->fromDate = $currentDate->format('M 1, Y');
            $groupLessonSearchModel->toDate = $currentDate->format('M t, Y'); 
            $groupLessonSearchModel->dateRange = $groupLessonSearchModel->fromDate . ' - ' . $groupLessonSearchModel->toDate;
            $searchModel->fromDate = $currentDate->format('M 1, Y');
            $searchModel->toDate = $currentDate->format('M t, Y'); 
            $searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
        }
        $groupLessonSearchModel->load(Yii::$app->request->get());
        $searchModel->load(Yii::$app->request->get());
        $model->userId = $searchModel->userId;
        $groupLessonsQuery = $groupLessonSearchModel->search(Yii::$app->request->queryParams);
        $groupLessonsQuery->orderBy(['lesson.id' => SORT_ASC]);
        $lessonsQuery = $searchModel->search(Yii::$app->request->queryParams);
        $lessonsQuery->orderBy(['lesson.id' => SORT_ASC]);
        $model->load(Yii::$app->request->get());
        $lessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $lessonsQuery,
            'pagination' => false
        ]);
        $groupLessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $groupLessonsQuery,
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
	        $payment->reference = $model->reference;
            $payment->user_id = $searchModel->userId;
            $payment->payment_method_id = $model->payment_method_id;
            $payment->date = (new \DateTime($model->date))->format('Y-m-d H:i:s');
            if (round($payment->amount, 2) > 0.00) {
                $payment->save();
            }
	        $receiptModel                   =   new Receipt();
            $receiptModel->date             =   $model->date;
            $receiptModel->userId           =   $searchModel->userId;
            $receiptModel->locationId       =   $locationId;
            $receiptModel->receiptNumber    =   1;
            $receiptModel->save();
            $model->paymentId = $payment->id;
	        $model->receiptId = $receiptModel->id;
            $model->lessonIds = $searchModel->lessonIds;
            $model->groupLessonIds = $groupLessonSearchModel->lessonIds;
            $model->save();
            $paymentReceipts   =   PaymentReceipt::find()
                                    ->andWhere(['receiptId' => $receiptModel->id])->all();
            if(!empty($paymentReceipts)) {
                foreach($paymentReceipts as $paymentReceipt) {
                    if($paymentReceipt->objectType == Receipt::TYPE_INVOICE) {
                        $receiptInvoiceIds[]  =   $paymentReceipt->objectId;

                    } if($paymentReceipt->objectType == Receipt::TYPE_LESSON) {
                        $receiptLessonIds[]  =   $paymentReceipt->objectId;
                    }
                    $receiptPaymentIds[]  =   $paymentReceipt->paymentId;
                }
            }
            $paymentLessonLineItems  =   Lesson::find()->andWhere(['id'  => $receiptLessonIds]);
            $paymentInvoiceLineItems =   Invoice::find()->andWhere(['id' => $receiptInvoiceIds]);
            $paymentLessonLineItemsDataProvider = new ActiveDataProvider([
                'query' => $paymentLessonLineItems,
                'pagination' => false,
            ]);
            $paymentInvoiceLineItemsDataProvider = new ActiveDataProvider([
                'query' => $paymentInvoiceLineItems,
                'pagination' => false,
            ]);
            $searchModel->showCheckBox = false;
            $printData = $this->renderAjax('/receive-payment/print/_form', [
                'model' => $model,
                'invoiceLineItemsDataProvider' => $paymentInvoiceLineItemsDataProvider,
                'lessonLineItemsDataProvider' =>   $paymentLessonLineItemsDataProvider,
                'searchModel' => $searchModel,
                'receiptModel' => $receiptModel,
            ]);
            $response = [
                'status' => true,
                'data' => $printData,
            ];
        } else {
            $data = $this->renderAjax('/receive-payment/_form', [
                'model' => $model,
                'creditDataProvider' => $creditDataProvider,
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                'groupLessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
                'searchModel' => $searchModel,
                'groupLessonSearchModel' => $groupLessonSearchModel
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

    public function actionValidateUpdate()
    {
        $request = Yii::$app->request;
        $model = new PaymentEditForm();
        $model->load($request->post());
        if (!$model->amount) {
            $model->amount = 0.0;
        }
        return ActiveForm::validate($model);
    }
}
