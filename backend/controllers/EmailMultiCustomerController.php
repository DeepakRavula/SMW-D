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
use yii\data\ActiveDataProvider;
use common\models\TestEmail;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use common\components\controllers\BaseController;
/**
 * BlogController implements the CRUD actions for Blog model.
 */
class EmailMultiCustomerController extends BaseController
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['send', 'email-multi-customer' ],
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
                        'actions' => ['send', 'email-multi-customer'],
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
  
    
    public function actionEmailMultiCustomer($id)
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
}
