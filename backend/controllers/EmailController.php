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
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
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
                'only' => ['send', 'lesson', 'invoice', 'enrolment'],
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
                        'actions' => ['send', 'lesson', 'invoice', 'enrolment'],
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
                'pagination' => [
                    'pageSize' => 60
                ],
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
            ->joinWith('enrolment')
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
        $invoicePayments                     = Payment::find()
            ->joinWith(['invoicePayment ip' => function ($query) use ($model) {
                $query->andWhere(['ip.invoice_id' => $model->id]);
            }])
            ->orderBy(['date' => SORT_DESC]);
        if ($model->isProFormaInvoice()) {
            $invoicePayments->notCreditUsed();
        }
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
}
