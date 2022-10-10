<?php

namespace common\components\queue;
use Yii;
use yii\helpers\Console;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use common\models\discount\LessonDiscount;
use common\models\Enrolment;
use common\models\Lesson;

/**
 * Class OderNotification.
 */
class ConfirmBulkReschedule extends BaseObject implements RetryableJobInterface
{
    public $lesson;
    public $lessonDiscount;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        $this->lessonDiscount->id = null;
        $this->lessonDiscount->isNewRecord = true;
        $this->lessonDiscount->lessonId = $this->lesson; 
        $this->lessonDiscount->save();
        // $oneLesson = Lesson::findOne($this->lesson);
        // if ($oneLesson->enrolment->isEnableRescheduleInfo) {
        //     $enrolment = Enrolment::find()->andWhere(['courseId' => $oneLesson->course->id])->one();
        //     $enrolment->updateAttributes(['isEnableRescheduleInfo' => false]);
        // } 
        
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
