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
   
    
    public function actionEmailMultiCustomer()
    {
        $emailMultiCustomerModel = new EmailMultiCustomer();
        $emailMultiCustomerModel->setScenario(EmailMultiCustomer::SCENARIO_SEND_EMAIL_MULTICUSTOMER);
        $emailMultiCustomerModel->load(Yii::$app->request->get());
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
        $model = new EmailForm();
        $model->setScenario(EmailMultiCustomer::SCENARIO_SEND_EMAIL_MULTICUSTOMER);            
        $data = $this->renderAjax('/mail/emailmulticustomer', [
            'model' => $model, 
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
            'error' => $emailMultiCustomerModel->getErrors('lessonIds'),
        ];
    }
}
}
