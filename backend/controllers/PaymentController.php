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
use common\models\Location;
use common\models\User;
use backend\models\search\ProformaInvoiceSearch;

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
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $amount = 0;
        $searchModel= new ProformaInvoiceSearch();
        $searchModel->showCheckBox = true;
        $model = new PaymentForm();
        $currentDate = new \DateTime();
        $model->date = $currentDate->format('M d,Y');
        $model->fromDate = $currentDate->format('M 1,Y');
        $model->toDate = $currentDate->format('M t,Y');
        $fromDate = new \DateTime($model->fromDate);
        $toDate = new \DateTime($model->toDate);
        $model->dateRange = $model->fromDate . ' - ' . $model->toDate;
        $paymentData = Yii::$app->request->get('PaymentForm');
        if ($paymentData) {
            $model->load(Yii::$app->request->get());
            if ($model->lessonId) {
                $lesson = Lesson::findOne($model->lessonId);
                $model->user_id = $lesson->customer->id;
            }
        }
        $lessonsQuery = Lesson::find();
        if ($model->lessonIds) {
            $lessonsQuery->andWhere(['id' => $model->lessonIds]);
        } else {
            $lessonsQuery->notDeleted()
                ->between($fromDate, $toDate)
                ->privateLessons()
                ->customer($model->user_id)
                ->isConfirmed()
                ->notCanceled()
                ->unInvoiced()
                ->location($locationId);
            $allLessons = $lessonsQuery->all();
            $lessonIds = [];
            foreach ($allLessons as $lesson) {
                if ($lesson->isOwing($lesson->enrolment->id)) {
                    $lessonIds[] = $lesson->id;
                }
            }
            $lessonsQuery = Lesson::find()
                ->andWhere(['id' => $lessonIds]);
        }
        $lessons = clone $lessonsQuery;
        foreach ($lessons->all() as $lesson) {
            $amount += $lesson->getOwingAmount($lesson->enrolment->id);
        }
        $lessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $lessonsQuery
        ]);
        $invoicesQuery = Invoice::find();
        if ($model->invoiceIds) {
            $invoicesQuery->andWhere(['id' => $model->invoiceIds]);
        } else {
            $invoicesQuery->notDeleted()
                ->lessonInvoice()
                ->location($locationId)
                ->customer($model->user_id)
                ->unpaid();
        }
        $invoices = clone $invoicesQuery;
        $amount += $invoices->sum('total');    
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoicesQuery
        ]);
        $model->amount = $amount;
        $request = Yii::$app->request;
        if ($request->post()) {
            $model->load($request->post());
            $payment = new Payment();
            $payment->amount = $model->amount;
            $payment->user_id = $model->user_id;
            $payment->payment_method_id = $model->payment_method_id;
            $payment->date = (new \DateTime($model->date))->format('Y-m-d H:i:s');
            $payment->save();
            $customer = User::findOne($model->user_id);
            foreach ($invoicesQuery->all() as $invoice) {
                $paymentModel = new Payment();
                $paymentModel->amount = $invoice->balance;
                if ($customer->hasCustomerCredit()) {
                    if ($paymentModel->amount > $customer->creditAmount) {
                        $paymentModel->amount = $customer->creditAmount;
                    }
                    $invoice->addPayment($customer, $paymentModel);
                } else {
                    break;
                }
            }
            foreach ($lessonsQuery->all() as $lesson) {
                $paymentModel = new Payment();
                $paymentModel->amount = $lesson->getOwingAmount($lesson->enrolment->id);
                if ($customer->hasCustomerCredit()) {
                    if ($paymentModel->amount > $customer->creditAmount) {
                        $paymentModel->amount = $customer->creditAmount;
                    }
                    $lesson->addPayment($customer, $paymentModel);
                } else {
                    break;
                }
            }
            $response = [
                'status' => true,
                'message' => 'Payment added succesfully'
            ];
        } else {
            $data = $this->renderAjax('/receive-payment/_form', [
                'model' => $model,
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                'searchModel'=> $searchModel,
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }
}
