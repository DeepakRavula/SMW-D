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
use common\models\ProformaInvoice;
use yii\helpers\Url;
use common\models\User;
use common\models\log\PaymentLog;

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
        $payment->setScenario(Payment::SCENARIO_EDIT);
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
        if ($payment->validate()) {
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
        } else {
            $response = [
                'status' => false,
                'message' => current($payment->getErrors())
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
        $model = $this->findModel($id);
        $model->setScenario(Payment::SCENARIO_DELETE);
        if ($model->validate()) {
            $model->delete();
            foreach ($model->invoicePayments as $invoicePayment) {
                $invoicePayment->invoice->save();
            }
            
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $paymentsAmount = Payment::find()
            ->exceptAutoPayments()
            ->exceptGiftCard()
            ->location($locationId)
            ->notDeleted()
            ->andWhere(['between', 'DATE(payment.date)', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), 
                (new \DateTime($searchModel->toDate))->format('Y-m-d')])
            ->sum('payment.amount');
        $this->layout = '/print';

        return $this->render('/report/payment/_print', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'paymentsAmount' => $paymentsAmount
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
                    'amount' => round(abs($invoiceCredit->balance), 2)
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
                        'amount' => round($paymentCredit->creditAmount, 2)
                    ];
                }
            }
        }
        
        $creditDataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'sort' => [
                'attributes' => ['id', 'type', 'reference', 'amount']
            ],
            'pagination' => false
        ]);
        return $creditDataProvider;
    }

    public function getUsedCredit($paymentCreditIds, $paymentCredits, $invoiceCreditIds, $invoiceCredits, $paymentId, $amount)
    { 
        $results = [];
        if (!empty($paymentCreditIds)) {                
            foreach ($paymentCreditIds as $key =>  $paymentCreditId) {
                $paymentCredit = Payment::findOne(['id' => $paymentCreditId]);
                $results[] = [
                    'id' => $paymentCredit->id,
                    'type' => 'Payment Credit',
                    'reference' => $paymentCredit->reference,
                    'amount' => round($paymentCredit->amount, 2),
                    'method' => $paymentCredit->paymentMethod->name,
                    'amountUsed' => round($paymentCredits[$key], 2),
                ];
            }  
        }
        if  (!empty($invoiceCreditIds)) {  
            foreach ($invoiceCreditIds as $key =>  $invoiceCreditId) {
                $invoiceCredit = Invoice::findOne(['id' => $invoiceCreditId]);
                $results[] = [
                    'id' => $invoiceCredit->id,
                    'type' => 'Invoice Credit',
                    'reference' => $invoiceCredit->getInvoiceNumber(),
                    'amount' => '',
                    'method' => '',
                    'amountUsed' => round($invoiceCredits[$key], 2),
                ];
            }
        } 
        $paymentNew = Payment::findOne(['id' => $paymentId]);
        if (!empty($paymentNew)) {
            $results[] = [
                'id' => $paymentId,
                'type' => 'Payment',
                'reference' => !empty($paymentNew->reference) ? $paymentNew->reference : null,
                'amount' => $paymentNew->amount,
                'method' => $paymentNew->paymentMethod->name,
                'amountUsed' => $amount,
            ]; 
        }
        $paymentsLineItemsDataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'sort' => [
                'attributes' => ['id', 'type', 'reference', 'amount', 'amountUsed']
            ],
            'pagination' => false
        ]);
        return $paymentsLineItemsDataProvider;
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
        $payment = new Payment();
        $currentDate = new \DateTime();
        $payment->date = $currentDate->format('M d, Y');
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
        $payment->user_id = $searchModel->userId;
        $groupLessonsQuery = $groupLessonSearchModel->search(Yii::$app->request->queryParams);
        $groupLessonsQuery->orderBy(['lesson.date' => SORT_ASC]);
        $lessonsQuery = $searchModel->search(Yii::$app->request->queryParams);
        $lessonsQuery->orderBy(['lesson.date' => SORT_ASC]);
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
        if (isset($model->invoiceIds)) {
            $invoicesQuery->andWhere(['id' => $model->invoiceIds]);
        } else {
            if (!$searchModel->userId) {
                $searchModel->userId = null;
            }
            $invoicesQuery->notDeleted()
                ->invoice()
                ->customer($searchModel->userId)
                ->unpaid()
                ->andWhere(['>','invoice.balance' , 0.09]);

        }
        $invoicesQuery->orderBy(['invoice.id' => SORT_ASC]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoicesQuery,
            'pagination' => false 
        ]);
        $creditDataProvider = $this->getAvailableCredit($searchModel->userId);
        if ($request->post()) {
            $model->load($request->post());
            $payment->load($request->post());
            $payment->amount = $model->amount;
            $payment->date = (new \DateTime($payment->date))->format('Y-m-d H:i:s');
            $payment->notes = $model->notes;
            if (round($payment->amount, 2) > 0.00) {
                $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
                $payment->on(Payment::EVENT_AFTER_INSERT, [new PaymentLog(), 'create'], ['loggedUser' => $loggedUser]);
                $payment->save();
            }
            
            $model->paymentId = $payment->id;
            $model->lessonIds = $searchModel->lessonIds;
            $model->groupLessonIds = $groupLessonSearchModel->lessonIds;
            $model->save();

            $paymentsLineItemsDataProvider = $this->getUsedCredit($model->paymentCreditIds, $model->paymentCredits, $model->invoiceCreditIds, $model->invoiceCredits, $model->paymentId, $model->amount);
            $printData = $this->renderAjax('/receive-payment/print/_form', [
                'model' => $model,
                'paymentModel' => $payment,
                'paymentsLineItemDataProvider' => $paymentsLineItemsDataProvider,
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                'groupLessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
                'searchModel' => $searchModel,
                'groupLessonSearchModel' => $groupLessonSearchModel,
            ]);
            $url = null;
            if ($model->prId) {
                $pr = ProformaInvoice::findOne($model->prId);
                if ($pr->isPaid()) {
                    $url = Url::to(['proforma-invoice/index']);
                }
            }
            $response = [
                'status' => true,
                'data' => $printData,
                'url' => $url,
            ];
        } else {
            $data = $this->renderAjax('/receive-payment/_form', [
                'model' => $model,
                'paymentModel' => $payment,
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

    public function actionCustomerPaymentView()
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

}
