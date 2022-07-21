<?php

namespace backend\controllers;

use backend\models\EmailForm;
use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\Location;
use common\models\Invoice;
use common\models\Lesson;
use common\models\GroupLesson;
use common\models\Enrolment;
use common\models\Student;
use common\models\EmailTemplate;
use common\models\EmailObject;
use backend\models\search\InvoiceSearch;
use yii\data\ActiveDataProvider;
use common\models\InvoiceLineItem;
use common\models\Payment;
use common\models\TestEmail;
use common\models\ProformaInvoice;
use backend\models\search\ProformaInvoiceSearch;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
use backend\models\PaymentForm;
use common\models\User;
use yii\data\ArrayDataProvider;
use backend\models\search\PaymentSearch;
use common\models\InvoicePayment;
use backend\models\search\PaymentFormLessonSearch;
use backend\models\search\PaymentFormGroupLessonSearch;
use common\components\queue\BulkEmail;
use common\models\log\CustomerStatementLog;
use common\models\CustomerStatement;
use common\models\log\LogActivity;
use common\models\log\LessonLog;
use common\models\log\InvoiceLog;
use common\models\log\PaymentLog;
use common\models\log\ReceivePaymentLog;
use common\models\CourseSchedule;
use common\models\NotifyViaEmail;
use common\models\NotificationEmailType;
use common\models\Course;
use common\models\CustomerEmailNotification;
/**
 * BlogController implements the CRUD actions for Blog model.
 */
class EmailController extends BaseController
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['send', 'lesson-bulk-email-send', 'lesson', 'invoice', 'enrolment', 'proforma-invoice', 'receipt', 'payment', 'customer-statement', 'group-enrolment-detail', 'notify-email', 'notify-email-preview'],
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
                        'actions' => ['send', 'lesson-bulk-email-send', 'lesson', 'invoice', 'enrolment', 'proforma-invoice', 'receipt', 'payment', 'customer-statement', 'group-enrolment-detail','notify-email', 'notify-email-preview'],
                        'roles' => ['administrator', 'staffmember', 'owner'],
                    ],
                ],
            ], 
        ];
    }
    public function actionSend()
    { 
       
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $location = Location::findOne(['id' => $locationId]);
        $objectId = Yii::$app->request->get('EmailForm')['objectId'];
        $userId = Yii::$app->request->get('EmailForm')['userId'];
        $model = new EmailForm();
        if ($model->load(Yii::$app->request->post())) { 
            $content = [];
                $content[] = Yii::$app->mailer->compose('content', [
                    'content' => $model->content,
                ])
                ->setFrom($location->email)
                ->setReplyTo($location->email)
                ->setSubject($model->subject)
                ->setBcc($model->bcc);
            Yii::$app->mailer->sendMultiple($content);
            if (!empty($model->invoiceId)) {
                $invoice = Invoice::findOne(['id' => $model->invoiceId]);
                $invoice->isSent = true;
                $invoice->save();
            }
            if (!empty($model->paymentRequestId)) {
                $proformaInvoice = ProformaInvoice::findOne(['id' => $model->paymentRequestId]);
                $proformaInvoice->isMailSent = true;
                $proformaInvoice->save();
            }
            $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
            if ($objectId == EmailObject::OBJECT_CUSTOMER_STATEMENT && $userId) {
                $customerStatement = new CustomerStatement();
                $customerStatement->userId = $userId;
                $customerStatement->on(CustomerStatement::EVENT_MAIL, [new CustomerStatementLog(), 'customerStatement'], ['loggedUser' => $loggedUser, 'activity' => LogActivity::TYPE_MAIL]);
                $customerStatement->trigger(CustomerStatement::EVENT_MAIL);
            } elseif ($objectId == EmailObject::OBJECT_LESSON) {
                $lesson = Lesson::findOne($model->lessonId);
                $lesson->userId = $userId;
                $lesson->on(Lesson::EVENT_LESSON_MAILED, [new LessonLog(), 'lessonMailed'], ['loggedUser' => $loggedUser]);
                $lesson->trigger(Lesson::EVENT_LESSON_MAILED);
            } elseif ($objectId == EmailObject::OBJECT_INVOICE) {
                $invoice = Invoice::findOne($model->invoiceId);
                $invoice->on(Invoice::EVENT_INVOICE_MAILED, [new InvoiceLog(), 'invoiceMailed'], ['loggedUser' => $loggedUser]);
                $invoice->trigger(Invoice::EVENT_INVOICE_MAILED);
            } elseif ($objectId == EmailObject::OBJECT_PAYMENT) {
                $payment = Payment::findOne($model->paymentId);
                $payment->on(Payment::EVENT_MAILED, [new PaymentLog(), 'paymentMailed'], ['loggedUser' => $loggedUser]);
                $payment->trigger(Payment::EVENT_MAILED);
            } elseif ($objectId == EmailObject::OBJECT_RECEIPT) {
                $user = User::findOne($userId);
                $user->on(PaymentForm::EVENT_TRANSACTION_MAILED, [new ReceivePaymentLog(), 'transactionMailed'], ['loggedUser' => $loggedUser]);
                $user->trigger(PaymentForm::EVENT_TRANSACTION_MAILED);
            }
            return [
                'status' => true,
                'message' => 'Mail has been sent successfully',
            ];
        }
    }

    public function actionEnrolment($id)
    {
        $model = Enrolment::findOne($id);
        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->andWhere(['courseId' => $model->course->id])
                ->scheduledOrRescheduled()
                ->isConfirmed()
                ->notDeleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
                'pagination' => false,
        ]);
        $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_COURSE]);
        $body = $emailTemplate->header ?? 'Please find the lesson schedule for the program you enrolled on ' . 
            Yii::$app->formatter->asDate($model->course->startDate);
        $emails = !empty($model->student->customer->email) ? $model->student->customer->email : null;
        $data = $this->renderAjax('/mail/enrolment', [
            'model' => new EmailForm(),
            'emails' => !empty($model->user->email) ?$model->user->email : null,
            'subject' => $emailTemplate->subject ?? 'Invoice from Arcadia Academy of Music',
            'emailTemplate' => $emailTemplate,
            'emails' => $emails,
            'lessonDataProvider' => $lessonDataProvider,
            'subject' => $emailTemplate->subject ?? 'Schedule for ' . $model->student->fullName,
            'content' => $body,
            'enrolmentModel' => $model,
            'userModel' => $model->student->customer
        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }

    public function actionLesson($id)
    {
        $model = Lesson::findOne($id);
        $students = Student::find()
            ->notDeleted()
            ->joinWith('enrolments')
            ->andWhere(['courseId' => $model->courseId])
            ->all();
        $emails = ArrayHelper::getColumn($students, 'customer.email', 'customer.email');
        $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_LESSON]);
        $data = $this->renderAjax('/mail/lesson', [
            'model' => new EmailForm(),
            'lessonModel' => $model,
            'emails' => $emails,
            'emailTemplate' => $emailTemplate,
            'subject' => $emailTemplate->subject ?? $model->course->program->name . ' lesson reschedule',
            'userModel' => !empty($model->enrolment->student->customer) ? $model->enrolment->student->customer : null,
        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }

    public function actionInvoice($id)
    {
        $model = Invoice::findOne($id);
        $searchModel = new InvoiceSearch();
        $searchModel->isPrint = false;
        $searchModel->toggleAdditionalColumns = false;
        $searchModel->isWeb = false;
        $searchModel->isMail = true;
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => InvoiceLineItem::find()
                ->notDeleted()
                ->andWhere(['invoice_id' => $model->id]),
            'pagination' => false,
        ]);
        if ($model->isProFormaInvoice()) {
            $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_PFI]);
        } else {
            $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_INVOICE]);
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
        $data = $this->renderAjax('/mail/invoice', [
            'model' => new EmailForm(),
            'emails' => !empty($model->user->email) ?$model->user->email : null,
            'subject' => $emailTemplate->subject ?? 'Invoice from Arcadia Academy of Music',
            'emailTemplate' => $emailTemplate,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
            'invoiceModel' => $model,
            'searchModel' => $searchModel,
            'userModel' => $model->user,
        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }
    public function actionProformaInvoice($id) {
        $model = ProformaInvoice::findOne($id);
	$searchModel = new ProformaInvoiceSearch();
	$searchModel->showCheckBox = false;
        $lessonLineItems = Lesson::find()
            ->joinWith(['proformaLessonItem' => function ($query) use ($model) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($model) {
                    $query->andWhere(['proforma_line_item.proformaInvoiceId' => $model->id]);
                }]);
            }]);
        $lessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $lessonLineItems,
            'pagination' => false,
        ]);
	$invoiceLineItems = Invoice::find()
            ->joinWith(['proformaInvoiceItem' => function ($query) use ($model) {
                $query->joinWith(['proformaLineItem' => function ($query) use ($model) {
                    $query->andWhere(['proforma_line_item.proformaInvoiceId' => $model->id]);
                }]);
            }]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
            'pagination' => false,
        ]);
            $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_PFI]);
       
        $data = $this->renderAjax('/mail/proforma-invoice', [
            'model' => new EmailForm(),
            'emails' => !empty($model->user->email) ?$model->user->email : null,
            'subject' => $emailTemplate->subject ?? 'Proforma Invoice from Arcadia Academy of Music',
            'emailTemplate' => $emailTemplate,
            'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
	    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'proformaInvoiceModel' => $model,
	    'searchModel' => $searchModel,
            'userModel' => $model->user,
        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }
    public function actionReceipt()
    {
        $model = new PaymentForm();
        $request = Yii::$app->request;
        if ($model->load($request->get())) {
            $customer = User::findOne(['id' => $model->userId]);
            $searchModel = new ProformaInvoiceSearch();
            $searchModel->showCheckBox = false;
            $paymentsLineItemsDataProvider = $model->getUsedCredit();
            $invoiceLineItemsDataProvider = $model->getInvoicesPaid();
            $lessonLineItemsDataProvider = $model->getLessonsPaid();
            $groupLessonLineItemsDataProvider = $model->getGroupLessonsPaid();
            $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_RECEIPT]);
            $paymentNew = Payment::findOne(['id' => $model->paymentId]);

            $data = $this->renderAjax('/mail/receipt', [
                'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'paymentsLineItemsDataProvider' => $paymentsLineItemsDataProvider,
                'groupLessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
                'searchModel' => $searchModel,
                'customer' => $customer,
                'model' => new EmailForm(),
                'emailTemplate' => $emailTemplate,
                'emails' => !empty($customer->email) ? $customer->email : null,
                'subject' => $emailTemplate->subject ?? 'Receipt from Arcadia Academy of Music',
                'payment' => $paymentNew,
                'paymentFormModel' => $model,
                'userModel' => $customer
            ]);
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }

    public function actionPayment($id) 
    {
        $model = Payment::findOne($id);
        $searchModel = new PaymentSearch();
        $lessonPayment = Lesson::find()
            ->privateLessons()
		    ->joinWith(['lessonPayments' => function ($query) use ($id) {
                $query->andWhere(['paymentId' => $id])
			->notDeleted();
            }]);
	    $lessonDataProvider = new ActiveDataProvider([
            'query' => $lessonPayment,
            'pagination' => false
        ]);

        $groupLessonPayment = GroupLesson::find()
            ->joinWith(['lessonPayments' => function ($query) use ($id) {
                $query->andWhere(['paymentId' => $id])
            ->notDeleted();
            }]);

        $groupLessonDataProvider = new ActiveDataProvider([
            'query' => $groupLessonPayment,
            'pagination' => false
        ]);
	    
        $invoicePayment = Invoice::find()
            ->notDeleted()
            ->joinWith(['invoicePayments' => function ($query) use ($id) {
                $query->andWhere(['payment_id' => $id])
			->notDeleted();
            }]);
	    
	    $invoiceDataProvider = new ActiveDataProvider([
            'query' => $invoicePayment,
            'pagination' => false
        ]);

        $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_PAYMENT]);
       
        $data = $this->renderAjax('/mail/payment', [
            'model' => new EmailForm(),
            'emails' => !empty($model->user->email) ?$model->user->email : null,
            'subject' => $emailTemplate->subject ?? 'Payment from Arcadia Academy of Music',
            'emailTemplate' => $emailTemplate,
            'lessonDataProvider' => $lessonDataProvider,
	        'invoiceDataProvider' => $invoiceDataProvider,
            'paymentModel' => $model,
	        'searchModel' => $searchModel,
            'userModel' => $model->user,
            'groupLessonDataProvider' =>  $groupLessonDataProvider

        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    } 
    
    public function actionCustomerStatement($id) 
    {
            $groupLessonSearchModel = new PaymentFormGroupLessonSearch();
            $groupLessonSearchModel->showCheckBox = true;
            $groupLessonSearchModel->userId = $id;
            $currentDate = new \DateTime();
            $searchModel = new PaymentFormLessonSearch();
            $searchModel->showCheckBox = true;
            $searchModel->userId = $id;
            $groupLessonSearchModel->fromDate = $currentDate->format('M 1, Y');
            $groupLessonSearchModel->toDate = $currentDate->format('M t, Y'); 
            $groupLessonSearchModel->dateRange = $groupLessonSearchModel->fromDate . ' - ' . $groupLessonSearchModel->toDate;
            $searchModel->fromDate = $currentDate->format('M 1, Y');
            $searchModel->toDate = $currentDate->format('M t, Y'); 
            $searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
            $groupLessonsQuery = $groupLessonSearchModel->search(Yii::$app->request->queryParams);
            $groupLessonsQuery->orderBy(['lesson.date' => SORT_ASC]);
            $lessonsQuery = $searchModel->search(Yii::$app->request->queryParams);
            $lessonsQuery->orderBy(['lesson.date' => SORT_ASC]);
            $lessonLineItemsDataProvider = new ActiveDataProvider([
                'query' => $lessonsQuery,
                'pagination' => false
            ]);
            $groupLessonLineItemsDataProvider = new ActiveDataProvider([
                'query' => $groupLessonsQuery,
                'pagination' => false
            ]);
            $invoicesQuery = Invoice::find();
            if (!$searchModel->userId) {
                $searchModel->userId = null;
            }
            $invoicesQuery->notDeleted()
                ->invoice()
                ->customer($searchModel->userId)
                ->unpaid()
                ->andWhere(['>','invoice.balance' , 0.09]);
            $invoicesQuery->orderBy(['invoice.id' => SORT_ASC]);
            $invoiceLineItemsDataProvider = new ActiveDataProvider([
                'query' => $invoicesQuery,
                'pagination' => false 
            ]);
            $creditDataProvider = $this->getAvailableCredit($searchModel->userId);

            $lessonsDue = $lessonsQuery->sum('private_lesson.balance');
            $invoicesDue = $invoicesQuery->sum('invoice.balance');
            $groupLessonsDue = $groupLessonsQuery->sum('group_lesson.balance');
            $credits = 0.00;
            $creditResults = $creditDataProvider->getModels();   
            foreach ($creditResults as $creditResult) {
                $credits+= $creditResult['amount'];
            }    
            $total = ($lessonsDue+$invoicesDue+$groupLessonsDue) - $credits;
            $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_CUSTOMER_STATEMENT]);
            $user = User::findOne($id);
            $customerStatement = new CustomerStatement();
            $customerStatement->userId = $id;
            $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
            $customerStatement->on(CustomerStatement::EVENT_PRINT, [new CustomerStatementLog(), 'customerStatement'], ['loggedUser' => $loggedUser, 'activity' => LogActivity::TYPE_MAIL]);
            $data = $this->renderAjax('/mail/_customer-statement', [
            'model' => new EmailForm(),
            'emails' => !empty($user->emails) ? $user->emailNames : null,
            'subject' => $emailTemplate->subject ?? 'Customer Statement from Arcadia Academy of Music',
            'emailTemplate' => $emailTemplate,
            'userModel' => $user,
            'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
            'groupLessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'creditDataProvider' => $creditDataProvider,
            'searchModel' => $searchModel,
            'groupLessonSearchModel' => $groupLessonSearchModel,
            'total' =>$total,
        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }

    
    public function actionGroupEnrolmentDetail($enrolmentId) 
    {
            $model = Enrolment::findOne($enrolmentId);
            $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_CUSTOMER_STATEMENT]);
            $user = User::findOne($model->student->customer->id);
            $scheduleHistoryDataProvider = new ActiveDataProvider([
                'query' => CourseSchedule::find()
                ->andWhere(['courseId' => $model->courseId]),
            ]);
            $lessonCount = Lesson::find()
                ->andWhere(['courseId' => $model->course->id])
                ->notDeleted()
                ->scheduledOrRescheduled()
                ->notCompleted()
                ->count();
            $query = Lesson::find()
            ->andWhere(['lesson.courseId' => $model->course->id])
            ->scheduledOrRescheduled()
            ->isConfirmed()
            ->notDeleted()
            ->notCompleted();
            if ($model->course->isPrivate()) {
                $query->orderBy([
                    'lesson.dueDate' => SORT_ASC,
                    'lesson.date' => SORT_ASC      
                    ]);
            } else {
                $query->joinWith(['groupLesson' => function ($query) use ($enrolmentId) {
                    $query->enrolment($enrolmentId)
                    ->notDeleted();
                }])
                    ->orderBy([
                    'group_lesson.dueDate' => SORT_ASC,
                    'lesson.date' => SORT_ASC      
                    ]);
            }
            $lessonDataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
            ]);
            $data = $this->renderAjax('/mail/_group-enrolment', [
                'model' => new EmailForm(),
                'emails' => !empty($user->email) ? $user->email : null,
                'subject' => $emailTemplate->subject ?? 'Customer Statement from Arcadia Academy of Music',
                'emailTemplate' => $emailTemplate,
                'userModel' => $user,
                'lessonDataProvider' => $lessonDataProvider,
                'enrolmentModel' => $model,
        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
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
    
    public function getCustomerPayments($customerId)
    {
        return Payment::find()
            ->notDeleted()
            ->exceptAutoPayments()
            ->customer($customerId)
            ->orderBy(['payment.id' => SORT_ASC])
            ->all();
    }

    public function actionLessonBulkEmailSend()
    { 
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $location = Location::findOne(['id' => $locationId]);
        $objectId = Yii::$app->request->get('EmailForm')['objectId'];
        $userId = Yii::$app->request->get('EmailForm')['userId'];
        $model = new EmailForm();
        if ($model->load(Yii::$app->request->post())) {   
            foreach ($model->to as $email) {
                Yii::$app->queue->push(new BulkEmail([
                    'to' => $email,
                    'subject' => $model->subject,
                    'locationEmail' => $location->email,
                    'content' => $model->content,
                ]));    
            }
            return [
                'status' => true,
                'message' => 'Mail has been sent successfully',
            ];
        }
    }
    public function actionNotifyEmail($customerId)
    {
        $model = new NotificationEmailType();
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $user = User::findOne($customerId);
        $searchModel = new PaymentFormLessonSearch();
        $firstLesson = [];
        $searchModel->showCheckBox = true;
        $modelPf = new PaymentForm();
        $payment = new Payment();
        $currentDate = new \DateTime();
        $payment->date = $currentDate->format('M d, Y');
        $modelPf->date = $currentDate->format('M d, Y');

        $searchModel->fromDate = $currentDate->format('M 1, Y');
        $searchModel->toDate = $currentDate->format('M t, Y');
        $searchModel->dateRange = $searchModel->fromDate . ' - ' . $searchModel->toDate;
        $searchModel->load(Yii::$app->request->get());
        $modelPf->userId = $customerId;
        $payment->user_id = $customerId;

        if ($model->load(Yii::$app->request->post())) {
            $notificationEmailType = Yii::$app->request->post();
            foreach ($notificationEmailType as $type) {
                if ($type == 1) {
                    print_r("Upcomming Makeup Lessons");

                }
                elseif ($type == 2) {
                    // print_r("First Schedule Lesson");
                    $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_LESSON]);
                    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
                    // $courses = Course::find()
                    //             ->regular()
                    //             ->confirmed()
                    //             // ->customer($customerId)
                    //             ->location($locationId)
                    //             ->privateProgram()
                    //             ->notDeleted()
                    //             ->all();
                    // foreach($courses as $course){
                    // // print_r($course);
                    // $coursea []= $course->id ;}
                    $courses = Course::find()
                        ->regular()
                        ->confirmed()
                        ->location($locationId)
                        ->privateProgram()
                        ->notDeleted()
                        ->all();

                    foreach ($courses as $course) {
                        $firstLesson = Lesson::find()
                            ->andWhere(['lesson.courseId' => $course->id])
                            ->orderBy(['lesson.date' => SORT_ASC])
                            ->notCanceled()
                            ->customer($customerId)
                            ->notDeleted()
                            ->isConfirmed()
                            ->notRescheduled()
                            ->regular()
                            ->limit(1);
                    // print_r($firstLesson);
                    // print_r("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@");
                    }
                    // print_r($firstLesson);
                    $firstLessonDataProvider = new ActiveDataProvider([
                        'query' => $firstLesson,
                        'pagination' => false
                    ]);
                    print_r("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@");
                    print_r($firstLessonDataProvider);
                    $data = $this->renderAjax('/mail/notify-via-email/notify_first_lesson_form', [
                        'model' => new EmailForm(),
                        'emails' => !empty($user->emails) ? $user->emailNames : null,
                        'subject' => $emailTemplate->subject ?? 'Customer Statement from Arcadia Academy of Music',
                        'emailTemplate' => $emailTemplate,
                        'userModel' => $user,
                        'firstLessonDataProvider' => $firstLessonDataProvider,
                        'searchModel' => $searchModel,
                    ]);
                    return [
                        'status' => true,
                        'data' => $data
                    ];
                }
                elseif ($type == 3) {
                    // print_r("OverDue Invoice");
                    $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_INVOICE]);
                    if (!$searchModel->userId) {
                        $searchModel->userId = null;
                    }
                    $invoicesQuery = Invoice::find()
                        ->invoice()
                        ->customer($customerId)
                        ->unpaid()
                        ->andWhere(['<', 'DATE(invoice.dueDate)', (new \DateTime())->format('Y-m-d')])
                        ->andWhere(['>', 'invoice.balance', 0.09])
                        ->orderBy(['invoice.id' => SORT_ASC])
                        ->notDeleted();

                    $invoiceLineItemsDataProvider = new ActiveDataProvider([
                        'query' => $invoicesQuery,
                        'pagination' => false
                    ]);

                    $data = $this->renderAjax('/mail/notify-via-email/notify_invoice_from', [
                        'model' => new EmailForm(),
                        'emails' => !empty($user->emails) ? $user->emailNames : null,
                        'subject' => $emailTemplate->subject ?? 'Customer Statement from Arcadia Academy of Music',
                        'emailTemplate' => $emailTemplate,
                        'userModel' => $user,
                        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                        'lessonLineItemsDataProvider' => null,
                        'searchModel' => $searchModel,
                    ]);
                    return [
                        'status' => true,
                        'data' => $data
                    ];
                }
                else {
                    // print_r("future Lessons");
                    $lessonsQuery = Lesson::find()
                        ->location($locationId)
                        ->joinWith(['privateLesson' => function ($query) {
                        $query->andWhere(['>', 'private_lesson.balance', 0.09]);
                    }])
                        ->andWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d')])
                        ->orderBy(['lesson.id' => SORT_ASC])
                        ->privateLessons()
                        ->notCanceled()
                        ->notDeleted()
                        ->customer($customerId)
                        ->isConfirmed()
                        ->regular();

                    $lessonLineItemsDataProvider = new ActiveDataProvider([
                        'query' => $lessonsQuery,
                        'pagination' => false
                    ]);


                    $creditDataProvider = $this->getAvailableCredit($searchModel->userId);
                    $credits = 0.00;
                    $creditResults = $creditDataProvider->getModels();
                    foreach ($creditResults as $creditResult) {
                        $credits += $creditResult['amount'];
                    }

                    $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_LESSON]);
                    $lessonsDue = $lessonsQuery->sum('private_lesson.balance');
                    $total = ($lessonsDue) - $credits;
                    $data = $this->renderAjax('/mail/notify-via-email/notify_future_lesson_from', [
                        'model' => new EmailForm(),
                        'emails' => !empty($user->emails) ? $user->emailNames : null,
                        'subject' => $emailTemplate->subject ?? 'Customer Statement from Arcadia Academy of Music',
                        'emailTemplate' => $emailTemplate,
                        'userModel' => $user,
                        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                        'searchModel' => $searchModel,
                        'total' => $total,
                    ]);
                    return [
                        'status' => true,
                        'data' => $data
                    ];
                }
            }



        }
    }
    public function actionNotifyEmailPreview($id)
    {

        $emailTypes = new NotificationEmailType();
        $data = $this->renderAjax('/mail/notify-email-types', [
            'emailTypes' => $emailTypes,
            'customerId' => $id,
        ]);
        return [
            'status' => true,
            'data' => $data
        ];
    }
}
