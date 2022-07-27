<?php

namespace console\controllers;


use yii\console\Controller;
use common\models\CustomerEmailNotification;
use yii\helpers\ArrayHelper;
use common\models\UserEmail;
use common\models\Lesson;
use common\models\Enrolment;
use yii\data\ActiveDataProvider;
use Yii;

class EmailController extends Controller
{
    public function actionAutoEmail()
    {
        $firstLessonCourseIds = [];
        $sendEmails = CustomerEmailNotification::find()
            ->andWhere(['isChecked' => true])
            ->groupBy('userId')
            ->all();

        foreach ($sendEmails as $sendEmail) {
            $customerId = $sendEmail->userId;
            print_r("\n");
            print_r($customerId);
            print_r("\n");

            $firstScheduledLesson = Enrolment::find()
                ->customer($customerId)
                ->joinWith(['firstLesson'])
                ->notDeleted()
                ->isConfirmed()
                ->all();

            $lessonQuery = Lesson::find()
                ->andWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')])
                ->orderBy(['lesson.id' => SORT_ASC])
                ->notCanceled()
                ->notDeleted()
                ->customer($customerId)
                ->isConfirmed()
                ->regular();

            $lessonDateTime = (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s');
            print_r($lessonDateTime);

            $emailNotificationTypes = CustomerEmailNotification::find()
                ->andWhere(['isChecked' => true])
                ->andWhere(['userId' => $customerId])
                ->all();

            foreach ($emailNotificationTypes as $emailNotificationType) {

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

                if ($type == CustomerEmailNotification::MAKEUP_LESSON) {
                    print_r("Upcommig Makeup Lesson");

                    $mailContent = $lessonQuery->rescheduled();

                }
                elseif ($type == CustomerEmailNotification::FIRST_SCHEDULE_LESSON) {
                    print_r("First Schedule Lessons");


                    foreach ($firstScheduledLesson as $record) {

                        print_r($record->firstLesson->id);
                        $firstLessonCourseIds[] = $record->firstLesson->id;

                    }

                    $mailContent = $lessonQuery->andWhere(['IN', 'lesson.id', $firstLessonCourseIds]);

                }
                elseif ($type == CustomerEmailNotification::OVERDUE_INVOICE) {
                    print_r("OverDue Invoice");

                }
                else {
                    print_r("Future Lessons");

                    foreach ($firstScheduledLesson as $record) {

                        print_r($record->firstLesson->id);
                        $firstLessonCourseIds[] = $record->firstLesson->id;

                    }

                    $mailContent = $lessonQuery->andWhere(['NOT IN', 'lesson.id', $firstLessonCourseIds]);
                }

                $requiredLessons = $mailContent
                    ->andWhere(['<', 'lesson.date', $lessonDateTime]);

                
                if ($requiredLessons) {

                    print_r(' requiredLessons ');
                    $dateProvider = new ActiveDataProvider([
                        'query' => $requiredLessons,
                        'pagination' => false
                    ]);
                    $sendEmail = [];

                    $sendMail[] = Yii::$app->mailer->compose('@backend/views/email-template/auto-notify-html', [
                        'contents' => $dateProvider,
                    ])
                        ->setFrom(env('ADMIN_EMAIL'))
                        ->setTo($mailIds)
                        ->setReplyTo(env('NOREPLY_EMAIL'))
                        ->setSubject('Notification for the upcoming lessons.');
                    Yii::$app->mailer->sendMultiple($sendMail);
                }
            }
        }
    }
}