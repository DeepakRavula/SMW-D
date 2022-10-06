<?php

namespace common\components\queue;

use common\models\Course;
use common\models\Enrolment;
use common\models\Lesson;
use Yii;
use common\models\User;
use yii\helpers\Console;
use common\models\log\StudentLog;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use common\models\NotificationEmailType;
use common\models\PrivateLessonEmailStatus;

/**
 * Class OderNotification.
 */
class LessonConfirm extends BaseObject implements RetryableJobInterface
{
    public $courseId;
    public $userId;

    public $rescheduleBeginDate;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $loggedUser = User::findOne(['id' => $this->userId]);
        Yii::$app->user->setIdentity($loggedUser);
        $courseModel = Course::findOne($this->courseId);
        $lessons = Lesson::find()
            ->notDeleted()
            ->andWhere(['courseId' => $courseModel->id])
            ->notConfirmed()
            ->orderBy(['lesson.date' => SORT_ASC])
            ->all();
        $emailNotifyTypes = NotificationEmailType::find()->all();
        foreach ($lessons as $lesson) {
            $lesson->isConfirmed = true;
            $lesson->save();
            if ($this->rescheduleBeginDate == null)
            {
                $lesson->setDiscount();
            }
            foreach($emailNotifyTypes as $emailNotifyType) {
                $emailStatus = new PrivateLessonEmailStatus();
                $emailStatus->lessonId = $lesson->id;
                $emailStatus->notificationType = $emailNotifyType->id;
                $emailStatus->status = false;
                $emailStatus->save();
            }
        }
        Lesson::triggerPusher();
       return true;
    }
    /**
     * @inheritdoc
     */
    public function getTtr()
    {
        return 60;
    }
    /**
     * @inheritdoc
     */
    public function canRetry($attempt, $error)
    {
        return $attempt < 1;
    }
}
