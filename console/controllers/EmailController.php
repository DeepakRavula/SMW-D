<?php

namespace console\controllers;


use yii\console\Controller;
use common\models\CustomerEmailNotification;
use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\Lesson;
use common\models\User;
use common\models\Enrolment;
use yii\data\ActiveDataProvider;
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
            $customerId = $sendEmail->userId;
            print_r("\n");
            print_r($customerId);
            print_r("\n");

            $user = User::findOne($sendEmail->userId);

            $firstScheduledLesson = Enrolment::find()
                        ->joinWith(['student' => function ($query) use ($customerId) {
                            $query->andWhere(['customer_id' => $customerId]);
                        }])
                        ->joinWith(['course' => function ($query) {
                            $query->andWhere(['>=', 'DATE(course.startDate)', (new \DateTime())->format('Y-m-d')]);
                        }])
                        ->notDeleted()
                        ->isConfirmed()
                        ->isRegular()
                        ->groupBy(['enrolment.id'])
                        ->activeAndfutureEnrolments();

            $lessonQuery = Lesson::find()
                            ->andWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d')])
                            ->orderBy(['lesson.id' => SORT_ASC])
                            ->notCanceled()
                            ->notDeleted()
                            ->customer($customerId)
                            ->isConfirmed()
                            ->regular();

            $lessonDateTime = (new \DateTime())->modify('+1 day')->format('Y-m-d');
            
            $emailNotificationTypes = CustomerEmailNotification::find()
                ->andWhere(['isChecked' => true])
                ->andWhere(['userId' => $customerId])
                ->all();

            foreach($emailNotificationTypes as $emailNotificationType) {

                $mailIds = ArrayHelper::map(UserEmail::find()
                    ->notDeleted()
                    ->joinWith('userContact')
                    ->andWhere(['user_contact.userId' => $customerId])
                    ->orderBy('user_email.email')
                    ->all(), 'email', 'email');

                $type = $emailNotificationType->emailNotificationTypeId;
                
                print_r($type);
                print_r("\n");
                print_r($mailIds); 
                print_r("\n");

                if($type == CustomerEmailNotification::MAKEUP_LESSON) {
                    print_r("Upcommig Makeup Lesson");

                    $mailContent = $lessonQuery->rescheduled();

                }
                elseif($type == CustomerEmailNotification::FIRST_SCHEDULE_LESSON) {
                    print_r("First Schedule Lessons");

                    $mailContent = $firstScheduledLesson;

                }
                elseif($type == CustomerEmailNotification::OVERDUE_INVOICE) {
                    print_r("OverDue Invoice");
                    
                }
                else {
                    print_r("Future Lessons");
                    $firstLessonCourseIds = [];
                    foreach($firstScheduledLesson as $record){
                        $firstLessonCourseIds[] = $record->course->firstLesson->id;
                    }

                    $mailContent =  $lessonQuery->andWhere(['NOT IN','lesson.id', $firstLessonCourseIds]);
                }

                $requiredLessons = $mailContent
                            ->andWhere(['OR', ['lesson.date' => $lessonDateTime], ['<', 'lesson.date', $lessonDateTime]])
                            ->all();
                    
                if($requiredLessons) {
                   print_r('require');
                   $sendMail = [];
                    $sendMail[]   =   \yii::$app->mailer->compose('/mail/auto-notify', [
                                'contents' => $requiredLessons,
                            ])
                            ->setFrom(env('ADMIN_EMAIL'))
                            ->setTo($mailIds)
                            ->setSubject('Notification for the upcoming lessons.');
                     Yii::$app->mailer->sendMultiple($sendMail);
                   
                }
            }
            
        }
        
    }
}