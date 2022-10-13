<?php

namespace common\components\queue;
use Yii;
use yii\helpers\Console;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use common\models\Lesson;

/**
 * Class OderNotification.
 */
class ConfirmBulkReschedule extends BaseObject implements RetryableJobInterface
{
    public $lessonId;
    public $rescheduledLessonId;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $oldLesson = Lesson::findOne($this->lessonId);
        $rescheduledLesson = Lesson::findOne($this->rescheduledLessonId);
        $oldLesson->makeAsChild($rescheduledLesson);
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
