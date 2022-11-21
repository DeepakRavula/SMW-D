<?php

namespace common\components\queue;
use Yii;
use yii\helpers\Console;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use common\models\discount\LessonDiscount;

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
        $lessonDiscounts = LessonDiscount::find()
            ->andWhere(['lessonId' => $this->lessonId])
            ->all();
        foreach ($lessonDiscounts as $lessonDiscount) {
            $lessonDiscount->id = null;
            $lessonDiscount->isNewRecord = true;
            $lessonDiscount->lessonId = $this->rescheduledLessonId;
            $lessonDiscount->save();
        }

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
