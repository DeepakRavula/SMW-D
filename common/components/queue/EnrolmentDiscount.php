<?php

namespace common\components\queue;

use common\models\Lesson;
use Yii;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use common\models\discount\LessonDiscount;
use common\models\discount\EnrolmentDiscount as Discount;

/**
 * Class OderNotification.
 */
class EnrolmentDiscount extends BaseObject implements RetryableJobInterface
{
    public $courseId;
    public $type;

    public $value;

    public $enrolmentId;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        try {
            if ((int)$this->type === (int)Discount::TYPE_PAYMENT_FREQUENCY) {
                $this->type = LessonDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY;
            }
            else {
                $this->type = LessonDiscount::TYPE_MULTIPLE_ENROLMENT;
            }
            $lessons = Lesson::find()
                ->notDeleted()
                ->andWhere(['courseId' => $this->courseId])
                ->notCompleted()
                ->isConfirmed()
                ->notCanceled()
                ->offset(12)
                ->joinWith(['privateLesson' => function ($query) {
                $query->andWhere(['>', 'private_lesson.balance', 0]);
            }])
                ->all();
            foreach ($lessons as $lesson) {
                print_r($lesson->id);
                $lessonDiscount = LessonDiscount::find()
                    ->andWhere(['type' => $this->type, 'lessonId' => $lesson->id, 'enrolmentId' => $this->enrolmentId])
                    ->one();
                if ($lessonDiscount) {
                    if ($lessonDiscount->isPfDiscount()) {
                        $lessonDiscount->value = $this->value;
                    }
                    else {
                        $lessonDiscount->value = $this->value / 4;
                    }
                    $lessonDiscount->save();
                }
                else {
                    $lessonDiscount = new LessonDiscount();
                    $lessonDiscount->lessonId = $lesson->id;
                    if ((int)$this->type === (int)LessonDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY) {
                        $lessonDiscount->type = LessonDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY;
                        $lessonDiscount->value = $this->value;
                        $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_PERCENTAGE;
                    }
                    else {
                        $lessonDiscount->type = LessonDiscount::TYPE_MULTIPLE_ENROLMENT;
                        $lessonDiscount->value = $this->value / 4;
                        $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_DOLLAR;
                    }
                    $lessonDiscount->enrolmentId = $this->enrolmentId;
                    $lessonDiscount->save();
                }
            }        
        } catch (\Exception $e) {
            print_r($e->getMessage());        
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
