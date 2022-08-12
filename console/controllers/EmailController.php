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
use common\models\Location;;
use common\models\EmailObject;
use common\models\EmailTemplate;
use common\models\Student;;
use common\models\PrivateLessonEmailStatus;
use common\models\GroupLessonEmailStatus;

class EmailController extends Controller
{
    public function actionAutoEmail()
    {
        $firstLessonCourseIds = [];
        $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_LESSON]);
        $locations = Location::find()->notDeleted()->all();
        foreach ($locations as $location) {
            $sendEmails = CustomerEmailNotification::find()->andWhere(['isChecked' => true])
                ->groupBy('userId')->all();

            foreach ($sendEmails as $sendEmail) {
                $customerId = $sendEmail->userId;

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

                    $privateLessons = Lesson::find()
                            ->andWhere(['>', 'lesson.date', $currentDateTime])
                            ->orderBy(['lesson.id' => SORT_ASC])
                            ->notCanceled()
                            ->notDeleted()
                            ->customer($customerId)
                            ->location($location->id)
                            ->isConfirmed()
                            ->regular()
                            ->privateLessons();
                    $groupLessons = Lesson::find()
                        ->andWhere(['>', 'lesson.date', $currentDateTime])
                        ->orderBy(['lesson.id' => SORT_ASC])
                        ->notCanceled()
                        ->notDeleted()
                        ->customer($customerId)
                        ->location($location->id)
                        ->isConfirmed()
                        ->regular()
                        ->groupLessons();
                        
                    $mailIds = ArrayHelper::map(UserEmail::find()
                        ->notDeleted()
                        ->joinWith('userContact')
                        ->andWhere(['user_contact.userId' => $customerId])
                        ->orderBy('user_email.email')
                        ->all(), 'email', 'email');

                    $type = $emailNotificationType->emailNotificationTypeId;
                    $message = $this->getMessage($type);
                    
                    if($privateLessons && $privateLessons->count() != 0){
                        $this->getPrivateNotify($privateLessons, $firstLessonCourseIds, $type, $message,$customerId, $location, $currentDateTime, $lessonDateTime, $emailTemplate, $mailIds);
                    } 
                    if($groupLessons &&  $groupLessons->count() != 0 ){
                        $this->getGroupNotify($groupLessons, $firstLessonCourseIds, $type, $message, $customerId, $location, $currentDateTime,$lessonDateTime, $emailTemplate, $mailIds );
                    }
                }
            }
        }
    }

    public function getMessage($type){
        $message = null;
        switch ($type) {
            case CustomerEmailNotification::MAKEUP_LESSON:
                $message = 'Makeup Lesson';
                break;
            case CustomerEmailNotification::FIRST_SCHEDULE_LESSON:
                $message = 'First Scheduled Lesson';
                break;
            case CustomerEmailNotification::OVERDUE_INVOICE:
                $message = 'Overdue Invoice';
                break;
            case CustomerEmailNotification::FUTURE_LESSON:
                $message = 'Future Lesson';
                break;
        }
        return $message;
    }

    public function getPrivateNotify($privateLessons, $firstLessonCourseIds, $type, $message, $customerId, $location, $currentTime, $lessonTime, $emailTemplate, $mailIds){
        if ($type == CustomerEmailNotification::MAKEUP_LESSON) {
            $mailContent = $privateLessons
                    ->rescheduled()
                    ->joinWith(['privateEmailStatus' => function($query){
                        $query->andWhere(['private_lesson_email_status.status' => false])
                        ->andWhere(['private_lesson_email_status.notificationType' => CustomerEmailNotification::MAKEUP_LESSON]);
                    }]);
        } elseif ($type == CustomerEmailNotification::FIRST_SCHEDULE_LESSON) {
            $mailContent = $privateLessons
                    ->andWhere(['IN', 'lesson.id', $firstLessonCourseIds])
                    ->scheduled()
                    ->joinWith(['privateEmailStatus' => function($query){
                        $query->andWhere(['private_lesson_email_status.status' => false])
                        ->andWhere(['private_lesson_email_status.notificationType' => CustomerEmailNotification::FIRST_SCHEDULE_LESSON]);
                    }]);
        } elseif ($type == CustomerEmailNotification::OVERDUE_INVOICE) {
            $mailContent =  Lesson::find()
                        ->andWhere(['>', 'lesson.date', $currentTime])
                        ->orderBy(['lesson.id' => SORT_ASC])
                        ->notCanceled()
                        ->notDeleted()
                        ->customer($customerId)
                        ->location($location->id)
                        ->privateLessons()
                        ->isConfirmed()
                        ->regular()
                        ->joinWith(['privateLesson' => function($query) {
                                $query->andWhere(['>', 'private_lesson.balance', 0.00]);
                            }])
                        ->joinWith(['privateEmailStatus' => function($query){
                            $query->andWhere(['private_lesson_email_status.status' => false])
                            ->andWhere(['private_lesson_email_status.notificationType' => CustomerEmailNotification::OVERDUE_INVOICE]);
                        }])
                        ->orderBy(['lesson.dueDate' => SORT_ASC]);
            $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_OVERDUE_LESSON]);
        } elseif ($type == CustomerEmailNotification::FUTURE_LESSON) {
            $mailContent = $privateLessons
                ->scheduled()
                ->andWhere(['NOT IN', 'lesson.id', $firstLessonCourseIds])
                ->joinWith(['privateEmailStatus' => function($query){
                    $query->andWhere(['private_lesson_email_status.status' => false])
                    ->andWhere(['private_lesson_email_status.notificationType' => CustomerEmailNotification::FUTURE_LESSON]);
                }]);
        }

        $requiredLessons = $mailContent
            ->andWhere(['AND', ['<=', 'lesson.date', $lessonTime], ['>', 'lesson.date', $currentTime]]);

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
                ->setSubject($emailTemplate->subject ?? "Remainder for tommorrow's Lesson ");
            if ($sendMail->send()) {
                foreach ($requiredLessons->all() as $data) {
                    $emailStatus = PrivateLessonEmailStatus::find()
                                    ->andWhere(['lessonId'=> $data->id])
                                    ->andWhere(['notificationType' => $type])
                                    ->one();
                    $emailStatus->updateAttributes(['status' => true]);
                }
            }
            sleep(5);
        }
    }

    public function getGroupNotify($groupLessons, $firstLessonCourseIds, $type, $message, $customerId, $location, $currentTime, $lessonTime, $emailTemplate, $mailIds){
       $groupStudentsId = [];
       $groupLessonData = $groupLessons->all();
       foreach($groupLessonData as $group){
            foreach($group->groupStudents as $student){
                $groupStudentsId[] = $student->id;
            }
       }
        if ($type == CustomerEmailNotification::MAKEUP_LESSON) {
            $mailContent = $groupLessons
                    ->rescheduled()
                    ->joinWith(['groupEmailStatus' => function($query) use ($groupStudentsId) {
                        $query->andWhere(['IN','group_lesson_email_status.studentId', $groupStudentsId])
                        ->andWhere(['group_lesson_email_status.status' => false])
                        ->andWhere(['group_lesson_email_status.notificationType' => CustomerEmailNotification::MAKEUP_LESSON]);
                    }]);
                  
        } elseif ($type == CustomerEmailNotification::FIRST_SCHEDULE_LESSON) {
            $mailContent = $groupLessons
                    ->andWhere(['IN', 'lesson.id', $firstLessonCourseIds])
                    ->joinWith(['groupEmailStatus' => function($query) use ($groupStudentsId){
                        $query->andWhere(['IN', 'group_lesson_email_status.studentId', $groupStudentsId])
                        ->andWhere(['group_lesson_email_status.status' => false])
                        ->andWhere(['group_lesson_email_status.notificationType' => CustomerEmailNotification::FIRST_SCHEDULE_LESSON]);
                    }]);
        } elseif ($type == CustomerEmailNotification::OVERDUE_INVOICE) {
            $mailContent =  Lesson::find()
                        ->andWhere(['>', 'lesson.date', $currentTime])
                        ->orderBy(['lesson.id' => SORT_ASC])
                        ->notCanceled()
                        ->notDeleted()
                        ->customer($customerId)
                        ->location($location->id)
                        ->groupLessons()
                        ->isConfirmed()
                        ->regular()
                        ->joinWith(['groupLesson' => function($query) {
                                $query->andWhere(['>', 'group_lesson.balance', 0.00]);
                            }])
                        ->joinWith(['groupEmailStatus' => function($query) use ($groupStudentsId){
                            $query->andWhere(['IN','group_lesson_email_status.studentId', $groupStudentsId])
                            ->andWhere(['group_lesson_email_status.status' => false])
                            ->andWhere(['group_lesson_email_status.notificationType' => CustomerEmailNotification::OVERDUE_INVOICE]);
                        }])
                        ->orderBy(['lesson.dueDate' => SORT_ASC]);
            $emailTemplate = EmailTemplate::findOne(['emailTypeId' => EmailObject::OBJECT_OVERDUE_LESSON]);
        } elseif ($type == CustomerEmailNotification::FUTURE_LESSON) {
            $mailContent = $groupLessons
                ->scheduled()
                ->andWhere(['NOT IN', 'lesson.id', $firstLessonCourseIds])
                ->joinWith(['groupEmailStatus' => function($query) use ($groupStudentsId){
                    $query->andWhere(['IN','group_lesson_email_status.studentId', $groupStudentsId])
                    ->andWhere(['group_lesson_email_status.status' => false])
                    ->andWhere(['group_lesson_email_status.notificationType' => CustomerEmailNotification::FUTURE_LESSON]);
                }]);
        }

        $requiredLessons = $mailContent
            ->andWhere(['AND', ['<=', 'lesson.date', $lessonTime], ['>', 'lesson.date', $currentTime]]);

        if ($requiredLessons && $requiredLessons->count() != 0) {
            $dataProvider = new ActiveDataProvider([
                'query' => $requiredLessons,
                'pagination' => false
            ]);
            foreach ($requiredLessons->all() as $lesson) {
            $groupLessonStudents = Student::find()
                ->notDeleted()
                ->groupCourseEnrolled($lesson->enrolment->course->id)->all();
                foreach($groupLessonStudents as $student){
                $sendMail = Yii::$app->mailer->compose('/mail/group-notify', [
                    'contents' => $dataProvider,
                    'message' => $message,
                    'type' => $type,
                    'emailTemplate' => $emailTemplate,
                    'date' => $lesson->date,
                    'courseName' => $lesson->course->program->name ?? null,
                    'teacherName' => $lesson->teacher->publicIdentity ?? null,
                    'studentName' => $student->first_name . $student->last_name ?? null,
                    'lessonId' => $lesson->id,
                ])
                    ->setFrom($location->email)
                    ->setTo($mailIds)
                    ->setReplyTo($location->email)
                    ->setSubject($emailTemplate->subject ?? "Remainder for tommorrow's Lesson ");
                if ($sendMail->send()) {
                        $emailStatus = GroupLessonEmailStatus::find()
                                ->andWhere(['lessonId' => $lesson->id])
                                ->andWhere(['studentId' => $student->id])
                                ->andWhere(['notificationType' => $type])
                                ->one();
                        $emailStatus->updateAttributes(['status' => true]);
                    }
                }
            }
            sleep(5);
        }
    }
}