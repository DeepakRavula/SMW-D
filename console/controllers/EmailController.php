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
use common\models\Location;
use common\models\EmailObject;
use common\models\EmailTemplate;

class EmailController extends Controller
{
    public function actionAutoEmail()
    {
        $firstLessonCourseIds = [];
        $locations = Location::find()->notDeleted()->all();
        $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_LESSON]);
        foreach ($locations as $location) {
            $sendEmails = CustomerEmailNotification::find()->andWhere(['isChecked' => true])
                ->groupBy('userId')->all();

            foreach ($sendEmails as $sendEmail) {
                $customerId = $sendEmail->userId;
                date_default_timezone_set('Asia/Kolkata');
                $lessonDateTime = (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s');
                $currentDateTime = (new \DateTime())->format('Y-m-d H:i:s');

                $emailNotificationTypes = CustomerEmailNotification::find()
                    ->andWhere(['isChecked' => true])
                    ->andWhere(['userId' => $customerId])->all();

                $requiredLessons;
                $message;
                foreach ($emailNotificationTypes as $emailNotificationType) {

                    $firstScheduledLesson = Enrolment::find()
                        ->customer($customerId)
                        ->joinWith(['firstLesson'])
                        ->notDeleted()
                        ->isConfirmed()
                        ->all();
                    foreach ($firstScheduledLesson as $record) {
                        $firstLessonCourseIds[] = $record->firstLesson->id;
                    }

                    $lessonQuery = Lesson::find()
                        ->andWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')])
                        ->orderBy(['lesson.id' => SORT_ASC])
                        ->andWhere(['lesson.auto_email_status' => false])
                        ->notCanceled()
                        ->notDeleted()
                        ->customer($customerId)
                        ->isConfirmed()
                        ->regular();

                    $mailIds = ArrayHelper::map(UserEmail::find()
                        ->notDeleted()
                        ->joinWith('userContact')
                        ->andWhere(['user_contact.userId' => $customerId])
                        ->orderBy('user_email.email')
                        ->all(), 'email', 'email');

                    $type = $emailNotificationType->emailNotificationTypeId;


                    if ($type == CustomerEmailNotification::MAKEUP_LESSON) {
                        $mailContent = $lessonQuery->rescheduled();
                        $message = 'Makeup Lesson';

                    } elseif ($type == CustomerEmailNotification::FIRST_SCHEDULE_LESSON) {
                        $mailContent = $lessonQuery->andWhere(['IN', 'lesson.id', $firstLessonCourseIds]);
                        $message = 'First Scheduled Lesson';

                    } elseif ($type == CustomerEmailNotification::OVERDUE_INVOICE) {
                       
                        $privateLesson = $lessonQuery->joinWith(['privateLesson' => function($query) {
                                $query->andWhere(['>', 'private_lesson.balance', 0.00]);
                            }]);
                        $groupLesson =   $lessonQuery->scheduled()->joinWith(['groupLesson' => function($query) {
                                $query->andWhere(['>', 'group_lesson.balance', 0.00]);
                            }]);
                        if(!empty($privateLesson)){
                            $mailContent = $privateLesson;
                        } else {
                            $mailContent = $groupLesson;
                        }
                        $mailContent = $mailContent->scheduled()->orderBy(['lesson.dueDate' => SORT_ASC]);
                        $message = 'Overdue Invoice';

                    } elseif ($type == CustomerEmailNotification::FUTURE_LESSON) {
                        $mailContent = $lessonQuery
                            ->scheduled()
                            ->andWhere(['NOT IN', 'lesson.id', $firstLessonCourseIds]);
                        $message = 'Future Lesson';
                    }

                    $requiredLessons = $mailContent
                        ->andWhere(['AND', ['<=', 'lesson.date', $lessonDateTime], ['>', 'lesson.date', $currentDateTime]]);

                    if ($requiredLessons && $requiredLessons->count() != 0) {
                        $dataProvider = new ActiveDataProvider([
                            'query' => $requiredLessons,
                            'pagination' => false
                        ]);

                        $sendMail = Yii::$app->mailer->compose('/mail/auto-notify', [
                            'contents' => $dataProvider,
                            'message' => $message,
                            'type' => $type,
                            'emailTemplate' => $emailTemplate,
                        ])
                            ->setFrom($location->email)
                            ->setTo($mailIds)
                            ->setReplyTo($location->email)
                            ->setSubject($emailTemplate->subject);
                        if ($sendMail->send()) {
                            foreach ($requiredLessons->all() as $data) {
                                $data->updateAttributes(['auto_email_status' => true]);
                            }
                        }
                        sleep(5);
                    }
                }
            }
        }
    }
}