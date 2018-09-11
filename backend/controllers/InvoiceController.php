<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use common\models\Item;
use common\models\InvoiceLineItem;
use backend\models\search\InvoiceSearch;
use common\models\Enrolment;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\UserProfile;
use common\models\Payment;
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\models\TaxCode;
use common\models\Location;
use yii\helpers\Json;
use yii\web\Response;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\InvoicePayment;
use common\models\Note;
use common\models\Course;
use common\models\PaymentCycle;
use common\models\InvoiceReverse;
use common\models\UserEmail;
use common\models\UserContact;
use common\models\Label;
use backend\models\search\UserSearch;
use common\models\log\LogHistory;
use backend\models\search\ItemSearch;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends BaseController
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => [
                    'delete', 'note', 'get-payment-amount', 'update-customer', 'post',
                    'create-walkin', 'fetch-user', 'add-misc', 'adjust-tax', 'mail',
                    'post-distribute', 'retract-credits', 'unpost', 'distribute',
                    'void', 'update', 'fetch-summary-and-status', 'compute-tax', 'show-items',
                    'edit-walkin'
                ],
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
                            'blank-invoice', 'index', 'mail', 'update-customer', 'create-walkin',
                            'note', 'view', 'fetch-user', 'add-misc','fetch-summary-and-status',
                            'compute-tax', 'create', 'update', 'delete', 'update-mail-status',
                            'all-completed-lessons', 'adjust-tax', 'revert-invoice', 'enrolment',
                            'group-lesson','get-payment-amount', 'void',
                            'post-distribute', 'retract-credits', 'unpost', 'distribute', 'post', 'show-items',
                            'edit-walkin'
                        ],
                        'roles' => [
                            'manageInvoices', 'managePfi'
                        ]
                    ]
                ]
            ]
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
            $searchModel->invoiceStatus = Invoice::STATUS_OWING;
            if (!empty($invoiceSearchRequest['dateRange'])) {
                $searchModel->dateRange = $invoiceSearchRequest['dateRange'];
            }
        } else {
            $searchModel->fromDate = (new \DateTime('first day of this month'))->format('M d,Y');
            $searchModel->toDate   = (new \DateTime('last day of this month'))->format('M d,Y');
            $searchModel->invoiceDateRange = $searchModel->fromDate.' - '.$searchModel->toDate;
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
        $invoice->type = Invoice::TYPE_INVOICE;
        $location = Location::findOne(['slug' => \Yii::$app->location]);
        if (!empty($invoiceRequest['customer_id'])) {
            $invoice->user_id = $invoiceRequest['customer_id'];
            $invoice->type = $invoiceRequest['type'];
        } else {
            $invoice->user_id = $location->walkinCustomer->customerId;
        }
        $invoice->location_id = $location->id;
        $invoice->createdUserId = Yii::$app->user->id;
        $invoice->updatedUserId = Yii::$app->user->id;
        $invoice->save();

        return $this->redirect(['view', 'id' => $invoice->id]);
    }

    public function actionUpdateCustomer($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $userSearchModel = new UserSearch();
        $userDataProvider = $userSearchModel->search($request->getQueryParams());
        $userDataProvider->pagination = false;
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        if ($request->isPost) {
            if ($model->load($request->post()) && $model->save()) {
                foreach ($model->payments as $payment) {
                    $payment->updateAttributes(['user_id' => $model->user_id]);
                }
                $response = [
                    'status' => true
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => current(ActiveForm::validate($model))
                ];
            }
        } else {
            $data = $this->renderAjax('customer/_list', [
                'model' => $model,
                'userDataProvider' => $userDataProvider,
                'searchModel' => $userSearchModel
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionCreateWalkin($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $customer = new User();
        $customer->locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $userProfile = new UserProfile();
        $userContact = new UserContact();
        $userEmail = new UserEmail();
        if ($request->isPost) {
            if ($userProfile->load($request->post()) && $userEmail->load($request->post())) {
                if ($customer->save()) {
                    $userProfile->user_id = $customer->id;
                    $userProfile->save();
                    if ($userEmail->email) {
                        $userContact->userId = $customer->id;
                        $userContact->isPrimary = true;
                        $userContact->labelId = Label::LABEL_WORK;
                        $userContact->save();
                        $userEmail->userContactId = $userContact->id;
                        $userEmail->save();
                    }
                    $model->user_id = $customer->id;
                    foreach ($model->payments as $payment) {
                        $payment->updateAttributes(['user_id' => $customer->id]);
                    }
                    $model->save();
                    $auth = Yii::$app->authManager;
                    $auth->assign($auth->getRole(User::ROLE_GUEST), $customer->id);
                    $response = [
                        'status' => true,
                        'message' => 'customer has been Added successfully.'
                    ];
                }
            }
        } else {
            $data = $this->renderAjax('customer/_walkin', [
                'model' => $model,
                'userModel' => $userProfile,
                'userEmail' => $userEmail
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionEditWalkin($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $customer = User::findOne($model->user_id);
        $customer->locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $userProfile = $customer->userProfile;
        $userContact = $customer->primaryContact ?? new UserContact();
        $userEmail = $customer->primaryEmail ?? new UserEmail();
        if ($request->isPost) {
            if ($userProfile->load($request->post()) && $userEmail->load($request->post())) {
                if ($customer->save()) {
                    $userProfile->save();
                    if ($userEmail->email) {
                        if ($userEmail->isNewRecord) {
                            $userContact->userId = $customer->id;
                            $userContact->isPrimary = true;
                            $userContact->labelId = Label::LABEL_WORK;
                            $userContact->save();
                            $userEmail->userContactId = $userContact->id;
                        }
                        $userEmail->save();
                    }
                    $model->save();
                    $response = [
                        'status' => true,
                        'message' => 'customer has been updated successfully.'
                    ];
                }
            }
        } else {
            $data = $this->renderAjax('customer/_walkin', [
                'model' => $model,
                'userModel' => $userProfile,
                'userEmail' => $userEmail
            ]);
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    }

    public function actionNote($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'status' => true,
            ];
        }
    }

    public function actionView($id)
    {
        $model                              = $this->findModel($id);
        $request                            = Yii::$app->request;
        $itemSearchModel                    = new ItemSearch();
        $itemSearchModel->avoidDefaultItems = true;
        $itemSearchModel->showAllItems      = false;
        $itemDataProvider                   = $itemSearchModel->search(Yii::$app->request->queryParams);
        $searchModel                        = new InvoiceSearch();
        $searchModel->load($request->get());
        $searchModel->isWeb = true;
        $searchModel->isMail = false;
        $invoiceLineItems                   = InvoiceLineItem::find()
            ->notDeleted()
            ->andWhere(['invoice_id' => $id]);
        $invoiceLineItemsDataProvider       = new ActiveDataProvider([
            'query' => $invoiceLineItems,
            'pagination' => false,
        ]);
        $invoiceRequest                     = $request->post('Invoice');
        $customerId                         = $invoiceRequest['customer_id'];

        if (isset($customerId)) {
            $customer = User::findOne(['id' => $customerId]);
            if ($customer->hasDiscount()) {
                foreach ($model->lineItems as $lineItem) {
                    if (!$lineItem->hasCustomerDiscount()) {
                        $model->addCustomerDiscount($customer);
                    }
                }
            }
        }
        
        $invoicePayments = InvoicePayment::find()
            ->notDeleted()
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->orderBy(['payment.date' => SORT_DESC]);
            }])
            ->invoice($id);
        $invoicePaymentsDataProvider = new ActiveDataProvider([
            'query' => $invoicePayments,
        ]);

        $notes = Note::find()
            ->andWhere(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_INVOICE])
            ->orderBy(['createdOn' => SORT_DESC]);

        $noteDataProvider = new ActiveDataProvider([
            'query' => $notes,
        ]);

        $customer  = User::findOne(['id' => $model->user_id]);
        $userModel = UserProfile::findOne(['user_id' => $model->user_id]);
        $user_id = $model->user_id;
        $userEmail = UserEmail::find()
            ->notDeleted()
            ->joinWith(['userContact uc' => function ($query) use ($user_id) {
                $query->andWhere(['uc.userId' => $user_id]);
            }])
            ->one();
            
        $logDataProvider= new ActiveDataProvider([
            'query' => LogHistory::find()->invoice($id) 
        ]);
        $searchModel->isPrint = false;
        return $this->render('view', [
                'model' => $model,
                'searchModel' => $searchModel,
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'customer' => empty($customer) ? new User() : $customer,
                'userModel' => $userModel,
                'userEmail' => $userEmail,
                'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
                'noteDataProvider' => $noteDataProvider,
                'itemDataProvider' => $itemDataProvider,
                'itemSearchModel' => $itemSearchModel,
                'logDataProvider' => $logDataProvider,
        ]);
    }

    public function actionAddMisc($id, $itemId)
    {
        $invoiceModel = $this->findModel($id);
        $itemModel = Item::findOne($itemId);
        $lineItem = $itemModel->addToInvoice($invoiceModel);
        $lineItem->invoice->save();

        return [
            'status' => true,
            'message' => 'Line item added successfully!'
        ];
    }

    public function actionFetchSummaryAndStatus($id)
    {
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $locationModel = Location::findOne(['id' => $locationId]);
        $today = (new \DateTime())->format('Y-m-d H:i:s');
        $data = Yii::$app->request->rawBody;
        $data = Json::decode($data, true);
        $taxCode = TaxCode::find()
            ->joinWith(['taxStatus' => function ($query) use ($data) {
                $query->andWhere(['tax_status.id' => $data['taxStatusId']]);
            }])
            ->andWhere(['<=', 'start_date', $today])
            ->andWhere(['province_id' => $locationModel->province_id])
            ->orderBy('start_date DESC')
            ->one();
        $grossPrice = $data['amount'] * $data['unit'];
        $rate = $grossPrice * $taxCode->rate / 100;

        return [
            'grossPrice' => $grossPrice,
            'total' => $grossPrice + $rate,
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
                ->notDeleted()
                ->joinWith(['enrolment' => function ($query) use ($studentIds) {
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
            $response = [
                'status' => true,
            ];
        } else {
            $response = [
                'status' => false,
                'errors' => ActiveForm::validate($model),
            ];
        }
        return $response;
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = Invoice::find()
                ->andWhere([
                    'invoice.id' => $id,
                    'location_id' => $locationId,
		    'isDeleted'=>false,
                ])
                ->one();
        if ($model !== null) {
            return $model;
	    
	    
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpdateMailStatus($id, $state)
    {
        $model = $this->findModel($id);
        $model->isSent = $state;
        $model->save();
    }

    public function actionAllCompletedLessons()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $query = Lesson::find()
                ->isConfirmed()
                ->notDeleted()
                ->location($locationId);
        $privateLessons = $query->completedUnInvoicedPrivate()->all();
        $groupLessons = $query->groupLessons()->completed()->all();
        foreach ($groupLessons as $lesson) {
            foreach ($lesson->enrolments as $enrolment) {
                if (!$enrolment->hasInvoice($lesson->id)) {
                    $lesson->createGroupInvoice($enrolment->id);
                }
            }
        }
            
        foreach ($privateLessons as $lesson) {
            $lesson->createPrivateLessonInvoice();
        }
        
        return $this->redirect(['index', 'InvoiceSearch[type]' => Invoice::TYPE_INVOICE]);
    }

    public function actionRevertInvoice($id)
    {
        $invoice                      = Invoice::findOne($id);
        $creditInvoice                = new Invoice();
        $creditInvoice->user_id       = $invoice->user_id;
        $creditInvoice->location_id   = $invoice->location_id;
        $creditInvoice->createdUserId = Yii::$app->user->id;
        $creditInvoice->updatedUserId = Yii::$app->user->id;
        $creditInvoice->type          = INVOICE::TYPE_INVOICE;
        $creditInvoice->save();
        $invoiceReverse                    = new InvoiceReverse();
        $invoiceReverse->invoiceId         = $invoice->id;
        $invoiceReverse->reversedInvoiceId = $creditInvoice->id;
        $invoiceReverse->save();
        foreach ($invoice->lineItems as $lineItem) {
            $newLineItem = clone $lineItem;
            $newLineItem->isNewRecord = true;
            $newLineItem->id = null;
            $newLineItem->setScenario(InvoiceLineItem::SCENARIO_OPENING_BALANCE);
            $newLineItem->invoice_id = $creditInvoice->id;
            $newLineItem->amount = $newLineItem->amount;
            $newLineItem->unit = - ($newLineItem->unit);
            $newLineItem->save();
            foreach ($lineItem->discounts as $discount) {
                $newDiscount = clone $discount;
                $newDiscount->isNewRecord = true;
                $newDiscount->id = null;
                $newDiscount->invoiceLineItemId = $newLineItem->id;
                $newDiscount->save();
            }
        }
        $creditInvoice->save();
        if ($invoice->isOwing()) {
            if ($invoice->balance > abs($creditInvoice->balance)) {
                $amount = abs($creditInvoice->balance);
            } else {
                $amount = $invoice->balance;
            }
            $payment = new Payment();
            $payment->amount = $amount;
            $invoice->addPayment($creditInvoice, $payment);
            $creditInvoice->save();
        }
        $invoice->isCanceled = true;
        $invoice->save();
        
        return $this->redirect(['view', 'id' => $creditInvoice->id]);
    }
    
    public function actionGroupLesson($lessonId, $enrolmentId = null)
    {
        $lesson      = Lesson::findOne($lessonId);
        if ($lesson->canInvoice()) {
            if (!empty($enrolmentId)) {
                $enrolment = Enrolment::findOne($enrolmentId);
                if (!$enrolment->hasInvoice($lessonId)) {
                    $lesson->createGroupInvoice($enrolmentId);
                    Yii::$app->session->setFlash('alert', [
                        'options' => ['class' => 'alert-success'],
                        'body' => 'Invoice has been successfully created',
                    ]);
                } else {
                    Yii::$app->session->setFlash('alert', [
                        'options' => ['class' => 'alert-success'],
                        'body' => 'Invoice has been created already!',
                    ]);
                }
                return $this->redirect(['lesson/view', 'id' => $lessonId, '#' => 'student']);
            } else {
                foreach ($lesson->enrolments as $enrolment) {
                    if (!$enrolment->hasInvoice($lessonId)) {
                        $lesson->createGroupInvoice($enrolment->id);
                    }
                }
                Yii::$app->session->setFlash('alert', [
                    'options' => ['class' => 'alert-success'],
                    'body' => 'Invoice has been successfully created',
                ]);
                return $this->redirect(['course/view', 'id' => $lesson->courseId]);
            }
        } else {
            Yii::$app->session->setFlash('alert', [
                'options' => ['class' => 'alert-danger'],
                'body' => 'Generate invoice against completed lesson only.',
            ]);
            if (!empty($enrolmentId)) {
                return $this->redirect(['lesson/view', 'id' => $lessonId, '#' => 'student']);
            } else {
                $course = Course::findOne($lesson->courseId);
                if ($course->isExtra()) {
                    $courseId = $course->regularCourse->id;
                } else {
                    $courseId = $lesson->courseId;
                }
                return $this->redirect(['course/view', 'id' => $courseId]);
            }
        }
    }

    public function actionGetPaymentAmount($id)
    {
        $model = Invoice::findOne($id);
        return [
            'status' => true,
            'amount' => Yii::$app->formatter->asDecimal($model->balance, 2),
        ];
    }
    
    public function actionAdjustTax($id)
    {
        $model = Invoice::findOne($id);
        if (!$model->isPosted) {
            $data = $this->renderAjax('_form-adjust-tax', [
                'model' => $model
            ]);
            $post = Yii::$app->request->post();
            if ($model->load($post)) {
                $model->isTaxAdjusted = false;
                $model->tax += $model->taxAdjusted;
                if ((float) $model->tax !== (float) $model->lineItemTax) {
                    $model->isTaxAdjusted = true;
                }

                if ($model->save()) {
                    $response = [
                        'status' => true,
                        'message' => 'Tax successfully updated!',
            ];
                } else {
                    $response = [
                        'status' => false,
                        'errors' => ActiveForm::validate($model),
                    ];
                }
            } else {
                $response = [
                    'status' => true,
                    'data' => $data,
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Tax cannot be updated if invoice posted!'
            ];
        }
        return $response;
    }


    public function actionVoid($id, $canbeUnscheduled)
    {
        $model = Invoice::findOne($id);
        if ($model->void($canbeUnscheduled)) {
            $response = [
                'status' => true
            ];
        } else {
            $response = [
                'status' => false
            ];
        }
        return $response;
    }

    public function actionShowItems($id)
    {
        $invoiceModel                       = $this->findModel($id);
        $request                            = Yii::$app->request;
        $itemSearchModel                    = new ItemSearch();
        $itemSearchModel->avoidDefaultItems = true;
        $itemSearchModel->showAllItems      = false;
        $itemDataProvider                   = $itemSearchModel->search($request->queryParams);
        $data = $this->renderAjax('_form-invoice-line-item', [
            'invoiceModel' => $invoiceModel,
            'itemDataProvider' => $itemDataProvider,
            'itemSearchModel' => $itemSearchModel,
        ]);
        $response = [
            'status' => true,
            'data' => $data
        ];
        return $response; 
    }
}
