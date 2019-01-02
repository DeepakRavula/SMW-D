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
use common\models\EmailMultiCustomer;
use common\models\UserEmail;
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
  
    
    public function actionEmailMultiCustomer()
    {
        $emailMultiCustomerModel = new EmailMultiCustomer();
        $emailMultiCustomerModel->load(Yii::$app->request->get());
        $emailMultiCustomerModel->setScenario(EmailMultiCustomer::SCENARIO_SEND_EMAIL_MULTICUSTOMER);
        if($emailMultiCustomerModel->validate()){
        $emails = ArrayHelper::map(UserEmail::find()
                    ->notDeleted()
                    ->joinWith(['userContact' => function ($query) use($emailMultiCustomerModel) {
                            $query->joinWith(['user' =>function ($query) use($emailMultiCustomerModel) { 
                                $query->joinWith(['student' =>function ($query) use($emailMultiCustomerModel) { 
                                    $query->joinWith(['lesson' =>function ($query) use($emailMultiCustomerModel) { 
                                        $query->andWhere(['lesson.id' => $emailMultiCustomerModel->lessonIds]);
                                    }]);
                                }]);   
                        }])
                        ->primary();
                    }])
                    ->orderBy('user_email.email')
                    ->all(), 'email', 'email');       
        $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_MESSAGE]);
        $data = $this->renderAjax('/mail/emailmulticustomer', [
            'model' => new EmailForm(),
            'emails' => !empty($emails) ?$emails : null,
            'subject' => $emailTemplate->subject ?? 'Message from Arcadia Academy of Music',
            'emailTemplate' => $emailTemplate,
        ]);
        $post = Yii::$app->request->post();
        if (!$post) {
            return [
                'status' => true,
                'data' => $data,
            ];
        }
       
    } else {
        return [
            'status' => false,
            'error' => $emailMultiCustomerModel->getErrors(),
        ];
    }
}
}
