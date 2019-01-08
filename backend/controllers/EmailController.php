<?php

namespace backend\controllers;

use backend\models\EmailForm;
use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use common\models\Location;
use common\models\Invoice;
use common\models\Lesson;
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
                'only' => ['send', 'lesson', 'invoice', 'enrolment', 'proforma-invoice', 'receipt', 'payment'],
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
                        'actions' => ['send', 'lesson', 'invoice', 'enrolment', 'proforma-invoice', 'receipt', 'payment'],
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
        $model = new EmailForm();
        if ($model->load(Yii::$app->request->post())) {
            $content = [];
                $content[] = Yii::$app->mailer->compose('content', [
                    'content' => $model->content,
                ])
                ->setFrom($location->email)
                ->setReplyTo($location->email)
                ->setSubject($model->subject);
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
        $model  = new PaymentForm();
        $request = Yii::$app->request;
        if ($model->load($request->get())) {
        $customer =  User::findOne(['id' => $model->userId]);
        $searchModel  =  new ProformaInvoiceSearch();
        $searchModel->showCheckBox = false;
        $paymentLessonLineItems  =   Lesson::find()->andWhere(['id'  => $model->lessonIds]);
        $paymentInvoiceLineItems =   Invoice::find()->andWhere(['id' => $model->invoiceIds]);
        $paymentGroupLessonLineItems = Lesson::find()->andWhere(['id' => $model->groupLessonIds]);
        $paymentLessonLineItemsDataProvider = new ActiveDataProvider([
        'query' => $paymentLessonLineItems,
        'pagination' => false,
    ]);
        $paymentInvoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $paymentInvoiceLineItems,
            'pagination' => false,
        ]);
        $groupLessonLineItemsDataProvider = new ActiveDataProvider([
            'query' => $paymentGroupLessonLineItems,
            'pagination' => false,
        ]);
        
        $results = [];
      if(!empty($model->paymentCreditIds))  {    
          $paymentCreditIds = $model->paymentCreditIds; 
          $paymentCredits   = $model->paymentCredits;          
      foreach($paymentCreditIds as $key =>  $paymentCreditId) {
          $paymentCredit = Payment::findOne(['id' => $paymentCreditId]);
          $results[] = [
            'id' => $paymentCredit->id,
            'type' => 'Payment Credit',
            'reference' => $paymentCredit->reference,
            'amount' => $paymentCredit->amount,
            'amountUsed' => $model->paymentCredits[$key],
        ];
      }  
    }
      if(!empty($invoiceCreditIds)) {  
        $invoiceCreditIds = $model->invoiceCreditIds; 
        $invoiceCredits   = $model->invoiceCredits;  
      foreach($invoiceCreditIds as $key =>  $invoiceCreditId) {
        $invoiceCredit = Invoice::findOne(['id' => $invoiceCreditId]);
        $results[] = [
            'id' => $invoiceCredit->id,
            'type' => 'Invoice Credit',
            'reference' => $invoiceCredit->getInvoiceNumber(),
            'amount' => abs($invoiceCredit->balance),
            'amountUsed' => $model->invoiceCredits[$key],
      ];
    }
    
} 
$paymentNew = Payment::findOne(['id' => $model->paymentId]);
if (!empty($paymentNew)) {
$results[] = [
    'id' => $paymentNew->id,
    'type' => 'Payment',
    'reference' => !empty($paymentNew->reference) ? $paymentNew->reference : null,
    'amount' => $paymentNew->amount,
    'amountUsed' => $model->amount,
]; 
}
     $paymentsLineItemsDataProvider = new ArrayDataProvider([
        'allModels' => $results,
        'sort' => [
            'attributes' => ['id', 'type', 'reference', 'amount', 'amountUsed']
        ]
     ]);

     $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_RECEIPT]);
     $data  =   $this->renderAjax('/mail/receipt', [
        'lessonLineItemsDataProvider' =>  $paymentLessonLineItemsDataProvider,
        'invoiceLineItemsDataProvider' =>  $paymentInvoiceLineItemsDataProvider,
        'paymentsLineItemsDataProvider'  =>  $paymentsLineItemsDataProvider,
        'searchModel'                  =>  $searchModel,
        'customer'                     =>   $customer,
        'model' => new EmailForm(),
        'emailTemplate'                =>   $emailTemplate,
        'emails' => !empty($customer->email) ?$customer->email : null,
        'subject' => $emailTemplate->subject ?? 'Receipt from Arcadia Academy of Music',
        'payment'                      => $paymentNew,
        'paymentFormModel'             => $model,
    ]);
    
    $post = Yii::$app->request->post();
    if (!$post) {
        return [
            'status' => true,
            'data' => $data,
        ];
    }
}
    }

    public function actionPayment($id) 
    {
        $model = Payment::findOne($id);
        $searchModel = new PaymentSearch();
        $lessonPayment = Lesson::find()
		    ->joinWith(['lessonPayments' => function ($query) use ($id) {
                $query->andWhere(['paymentId' => $id])
			->notDeleted();
            }]);
	    $lessonDataProvider = new ActiveDataProvider([
            'query' => $lessonPayment,
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
        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
    }   
}
