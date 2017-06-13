<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\InvoiceLineItem;
use backend\models\search\InvoiceSearch;
use backend\models\search\LessonSearch;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\UserProfile;
use common\models\Payment;
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\ItemType;
use common\models\TaxCode;
use common\models\Location;
use yii\helpers\Json;
use yii\web\Response;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Note;
use common\models\PaymentCycle;
use common\models\InvoiceReverse;
use common\models\InvoiceLog;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends Controller
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
			[
				'class' => 'yii\filters\ContentNegotiator',
				'only' => ['delete'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
        	],
        ];
    }

    /**
     * Lists all Invoice models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoiceSearch();
        $request = Yii::$app->request;
        $invoiceSearchRequest = $request->get('InvoiceSearch');
        if ((int) $invoiceSearchRequest['type'] === Invoice::TYPE_PRO_FORMA_INVOICE) {
            $currentDate                = new \DateTime();
            $searchModel->toDate        = $currentDate->format('d-m-Y');
            $fromDate                   = clone $currentDate;
            $fromDate                   = $fromDate->modify('-90 days');
            $searchModel->fromDate      = $fromDate->format('d-m-Y');
            $searchModel->invoiceStatus = Invoice::STATUS_OWING;
            $searchModel->mailStatus    = InvoiceSearch::STATUS_MAIL_NOT_SENT;
            $searchModel->dueFromDate      = $currentDate->format('1-m-Y');
            $searchModel->dueToDate        = $currentDate->format('t-m-Y');
            $searchModel->dateRange     = $searchModel->fromDate.' - '.$searchModel->toDate;
        } else {
            $searchModel->fromDate = (new \DateTime('first day of this month'))->format('d-m-Y');
            $searchModel->toDate   = (new \DateTime('last day of this month'))->format('d-m-Y');
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoice model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionBlankInvoice()
    {
        $invoice = new Invoice();
        $request = Yii::$app->request;
        $invoiceRequest = $request->get('Invoice');
        if (empty($invoiceRequest['customer_id'])) {
            $invoice->user_id = Invoice::USER_UNASSINGED;
            $invoice->type = Invoice::TYPE_INVOICE;
        }
        if (!empty($invoiceRequest['customer_id'])) {
            $invoice->user_id = $invoiceRequest['customer_id'];
            $invoice->type = $invoiceRequest['type'];
        }
        $location_id = Yii::$app->session->get('location_id');
        $invoice->location_id = $location_id;
		$invoice->createdUserId = Yii::$app->user->id;
		$invoice->updatedUserId = Yii::$app->user->id;
        $invoice->save();

        return $this->redirect(['view', 'id' => $invoice->id]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $request = Yii::$app->request;
        $searchModel = new InvoiceSearch();
        $searchModel->load($request->get());
        $invoiceLineItems = InvoiceLineItem::find()->where(['invoice_id' => $id]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
            'pagination' => false,
        ]);
        $invoiceRequest = $request->post('Invoice');
        $customerId = $invoiceRequest['customer_id'];
        if (!empty($model->user_id)) {
            $customer = User::findOne(['id' => $model->user_id]);
        }
        if (isset($customerId)) {
            $customer = User::findOne(['id' => $customerId]);
        }
        if (empty($customer)) {
            $customer = new User();
        }

        $customerInvoicePayments = Payment::find()
                ->joinWith(['invoicePayment ip' => function ($query) use ($model) {
                    $query->where(['ip.invoice_id' => $model->id]);
                }])
                ->where(['user_id' => $model->user_id]);

        $customerInvoicePaymentsDataProvider = new ActiveDataProvider([
            'query' => $customerInvoicePayments,
        ]);

        $invoicePayments = Payment::find()
                ->joinWith(['invoicePayment ip' => function ($query) use ($model) {
                    $query->where(['ip.invoice_id' => $model->id]);
                }])
                ->orderBy(['date' => SORT_DESC]);

        $invoicePaymentsDataProvider = new ActiveDataProvider([
            'query' => $invoicePayments,
        ]);

        if (!empty($model->user->userProfile->user_id)) {
            $userModel = UserProfile::findOne(['user_id' => $customer->id]);
        } else {
            $userModel = new UserProfile();
        }

        if ($request->isPost) {
            if (isset($_POST['customer-invoice'])) {
                if ($model->load($request->post())) {
                    $model->user_id = $customer->id;
                    $model->save();
                }
            }
            if (isset($_POST['guest-invoice'])) {
                if ($customer->load($request->post())) {
                    if ($customer->save()) {
                        $model->user_id = $customer->id;
                        $model->save();

                        if ($userModel->load($request->post())) {
                            $userModel->user_id = $customer->id;
                            $userModel->save();
                            $auth = Yii::$app->authManager;
                            if (empty($customer->id)) {
                                $auth->assign($auth->getRole(User::ROLE_GUEST), $customer->id);
                            }
                            Yii::$app->session->setFlash('alert', [
                                'options' => ['class' => 'alert-success'],
                                'body' => 'Invoice has been updated successfully',
                            ]);
                        }
                    }
                }
            }
        }
		if ($model->load(\Yii::$app->getRequest()->getBodyParams(), '') && $model->hasEditable && $model->save()) {
			$response = Yii::$app->response;
			$response->format = Response::FORMAT_JSON;
                return ['output' => $model->notes, 'message' => ''];
        }

        $notes = Note::find()
                ->where(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_INVOICE])
                ->orderBy(['createdOn' => SORT_DESC]);

        $noteDataProvider = new ActiveDataProvider([
            'query' => $notes,
        ]);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'invoicePayments' => $customerInvoicePaymentsDataProvider,
            'customer' => empty($customer) ? new User() : $customer,
            'userModel' => $userModel,
            'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
            'noteDataProvider' => $noteDataProvider
        ]);
    }

    public function actionAddMisc($id)
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $invoiceLineItemModel = new InvoiceLineItem();
        $userModel = User::findOne(['id' => Yii::$app->user->id]);
        $invoiceLineItemModel->on(InvoiceLineItem::EVENT_CREATE, [new InvoiceLog(), 'newLineItem']);
        $invoiceLineItemModel->userName = $userModel->publicIdentity;
        if ($invoiceLineItemModel->load(Yii::$app->request->post())) {
            $invoiceLineItemModel->invoice_id = $model->id;
            $invoiceLineItemModel->item_type_id = ItemType::TYPE_MISC;
            $invoiceLineItemModel->code        = $invoiceLineItemModel->getItemCode();
            $invoiceLineItemModel->cost        = 0.0;
            $invoiceLineItemModel->discount = 0.0;
			if($invoiceLineItemModel->tax_rate == '') {
				$invoiceLineItemModel->tax_rate = $invoiceLineItemModel->amount * ( 5 / 100);	
			}
            if ($invoiceLineItemModel->validate()) {
                $invoiceLineItemModel->save();
                $model->save();
                $invoiceLineItemModel->trigger(InvoiceLineItem::EVENT_CREATE);

                $response = [
                    'invoiceStatus' => $model->getStatus(),
                    'status' => true,
					'amount' => round($model->invoiceBalance, 2)
                ];
            } else {
                $invoiceLineItemModel = ActiveForm::validate($invoiceLineItemModel);
                $response = [
                    'status' => false,
                    'errors' => $invoiceLineItemModel,
                ];
            }

            return $response;
        }
    }

    public function actionFetchSummaryAndStatus($id)
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $summary = $this->renderPartial('_view-bottom-summary', [
            'model' => $model,
        ]);
        $InvoicePaymentDetails = $this->renderPartial('payment/_invoice-summary', [
            'model' => $model,
        ]);
        $status = $model->getStatus();

        return [
            'summary' => $summary,
            'status' => $status,
            'details' => $InvoicePaymentDetails,
        ];
    }

    public function actionComputeTax()
    {
        $response = \Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $locationId = Yii::$app->session->get('location_id');
        $locationModel = Location::findOne(['id' => $locationId]);
        $today = (new \DateTime())->format('Y-m-d H:i:s');
        $data = Yii::$app->request->rawBody;
        $data = Json::decode($data, true);
        $taxCode = TaxCode::find()
            ->joinWith(['taxStatus' => function ($query) use ($data) {
                $query->where(['tax_status.id' => $data['taxStatusId']]);
            }])
            ->where(['<=', 'start_date', $today])
            ->andWhere(['province_id' => $locationModel->province_id])
            ->orderBy('start_date DESC')
            ->one();
        $rate = $data['amount'] * $taxCode->rate / 100;

        return [
            'tax_type' => $taxCode->taxType->name,
            'code' => $taxCode->code,
            'rate' => $rate,
            'tax_status' => $data['taxStatusName'],
			'tax' => $taxCode->rate,
        ];
    }

    /**
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $invoiceRequest = $request->get('Invoice');
		$customerId = $invoiceRequest['customer_id'];
		$user = User::findOne(['id' => $customerId]);
		$studentIds = ArrayHelper::getColumn($user->student, 'id');
		$paymentCycleDataProvider = new ActiveDataProvider([
			'query' => PaymentCycle::find()
				->joinWith(['enrolment' => function($query) use($studentIds) {
					$query->andWhere(['studentId' => $studentIds]);
				}]),
			'pagination' => false,
		]);
		return $this->render('create', [
			'paymentCycleDataProvider' => $paymentCycleDataProvider
		]);
    }

    /**
     * Updates an existing Invoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-success'],
                'body' => 'Invoice has been updated successfully',
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
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
	{
		$model = $this->findModel($id);
		$userModel = User::findOne(['id' => Yii::$app->user->id]);
        $model->on(Invoice::EVENT_DELETE, [new InvoiceLog(), 'deleteInvoice']);
		$model->userName = $userModel->publicIdentity;
		$model->setScenario(Invoice::SCENARIO_DELETE);
		if ($model->validate()) {
			$model->delete();
			$model->trigger(Invoice::EVENT_DELETE);
			$response = [
				'status' => true,
				'url' => Url::to(['index', 'InvoiceSearch[type]' => $model->type]),
			];
		} else {
			$model	 = ActiveForm::validate($model);
			$response		 = [
				'errors' => $model['invoice-id'],
			];
		}
		return $response;
	}

	/**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Invoice the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $model = Invoice::find()
                ->where([
                    'invoice.id' => $id,
                    'location_id' => $locationId,
                ])
                ->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAddInvoice()
    {
        $model = new User();

        if (isset(Yii::$app->request->queryParams['User'])) {
            $model->customer = Yii::$app->request->queryParams['User']['customer'];
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    public function actionPrint($id)
    {
        $model = $this->findModel($id);
        $invoiceLineItems = InvoiceLineItem::find()->where(['invoice_id' => $id]);
        $payments = Payment::find()
            ->joinWith(['invoicePayments' => function ($query) use ($id) {
                $query->where(['invoice_id' => $id]);
            }])
            ->groupBy('payment.payment_method_id');
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
			'pagination' => false,
        ]);
        $paymentsDataProvider = new ActiveDataProvider([
            'query' => $payments,
        ]);
        $this->layout = '/print';

        return $this->render('_print', [
                    'model' => $model,
                    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                    'paymentsDataProvider' => $paymentsDataProvider,
        ]);
    }

    public function actionUpdateMailStatus($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->load($request->post());
        $model->save();
    }

    public function actionSendMail($id)
    {
        $model      = $this->findModel($id);
		$invoiceRequest = Yii::$app->request->post('Invoice');
		if($invoiceRequest) {
			$model->toEmailAddress = $invoiceRequest['toEmailAddress'];
			$model->subject = $invoiceRequest['subject'];
			$model->content = $invoiceRequest['content'];
			$isMailSend = $model->sendEmail();
			if($isMailSend)
			{
				Yii::$app->session->setFlash('alert', [
					'options' => ['class' => 'alert-success'],
					'body' => ' Mail has been send successfully',
				]);
			} else {
				Yii::$app->session->setFlash('alert', [
					'options' => ['class' => 'alert-danger'],
					'body' => 'The customer doesn\'t have email id',
				]);
			}
			return $this->redirect(['view', 'id' => $model->id]);
		}
    }

	public function actionAllCompletedLessons()
	{
            $locationId = Yii::$app->session->get('location_id');
            $lessons = Lesson::find()
                ->notDeleted()
                ->completedUnInvoiced()
                ->location($locationId)
                ->all();
            foreach($lessons as $lesson) {
                $lesson->createInvoice();
            }
		
            return $this->redirect(['index', 'InvoiceSearch[type]' => Invoice::TYPE_INVOICE]);
	}

    public function actionRevertInvoice($id)
    {
        $invoice                       = Invoice::findOne($id);
        $invoice->isCanceled           = true;
        $invoice->save();
        $creditInvoice                 = new Invoice();
        $creditInvoice->user_id        = $invoice->user_id;
        $creditInvoice->location_id    = $invoice->location_id;
		$creditInvoice->createdUserId = Yii::$app->user->id;
		$creditInvoice->updatedUserId = Yii::$app->user->id;
        $creditInvoice->type           = INVOICE::TYPE_INVOICE;
        $creditInvoice->save();
        $invoiceReverse                   = new InvoiceReverse();
        $invoiceReverse->invoiceId        = $invoice->id;
        $invoiceReverse->reversedInvoiceId = $creditInvoice->id;
        $invoiceReverse->save();
        $creditInvoice->addLineItem($invoice->lineItem->lesson);
        $creditInvoice->save();
        
        return $this->redirect(['view', 'id' => $creditInvoice->id]);
    }
    
    public function actionInvoicePaymentCycle($id)
    {
        $paymentCycle = PaymentCycle::findOne($id);

        if ($paymentCycle->canRaiseProformaInvoice()) {
            $paymentCycle->createProFormaInvoice();

            return $this->redirect(['view', 'id' => $paymentCycle->proFormaInvoice->id]);
        } else {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'ProForma-Invoice can be generated only for current and next payment cycle only.',
            ]);
            return $this->redirect(['enrolment/view', 'id' => $paymentCycle->enrolment->id, '#' => 'payment-cycle']);
        }
    }
}
