<?php

namespace console\controllers;


use yii\console\Controller;
use common\models\CustomerEmailNotification;
use common\models\Location;
use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\Lesson;
use common\models\log\LessonLog;
use common\models\User;
use Yii;

class EmailController extends Controller
{
    public function actionAutoEmail()
    {

        $sendEmails = CustomerEmailNotification::find()
                ->andWhere(['isChecked' => true])
                
                ->groupBy('userId')
                ->all();

        foreach($sendEmails as $sendEmail){

            print_r("\n");
            print_r($sendEmail->userId);
            print_r("\n");
            
            $emailNotificationTypes = CustomerEmailNotification::find()
                ->andWhere(['isChecked' => true])
                ->andWhere(['userId' => $sendEmail->userId])
                ->all();


            foreach($emailNotificationTypes as $emailNotificationType) {

                $data = ArrayHelper::map(UserEmail::find()
                    ->notDeleted()
                    ->joinWith('userContact')
                    ->andWhere(['user_contact.userId' => $sendEmail->userId])
                    ->orderBy('user_email.email')
                    ->all(), 'email', 'email');

                    
                $type = $emailNotificationType->emailNotificationTypeId;
                
                print_r($type);
                print_r("\n");
                print_r($data); 
                print_r("\n");
                if($type == 1) {
                    print_r("Upcommig Makeup Lesson");

                }
                elseif($type == 2) {
                    print_r("First Schedule Lessons");

                }
                elseif($type == 3) {
                    print_r("OverDue Invoice");
                    
                }
                else {
                    print_r("Future Lessons");
                    
                }

            }
            
        }
        
    }
}