<?php
namespace common\components\validators\reschedule;

use yii\validators\Validator;
use common\models\Course;
use Yii;

class CourseRescheduleBeginValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $course = Course::findOne($model->courseId);
        $courseStartDate = (new \DateTime())->format('Y-m-d');
        $courseEndDate = (new \DateTime($course->endDate))->format('Y-m-d');
        $startDate = (new \DateTime($model->rescheduleBeginDate))->format('Y-m-d');
        $endDate = (new \DateTime($model->rescheduleEndDate))->format('Y-m-d');
        if ($startDate < $courseStartDate || $startDate > $courseEndDate) {
            $this->addError($model, $attribute, 'Begin date should be within ' .
                Yii::$app->formatter->asDate($courseStartDate) . ' - ' .
                Yii::$app->formatter->asDate($courseEndDate));
        }
        if ($endDate < $startDate) {
            $this->addError($model, $attribute, 'Begin date should be lesser than end date');
        }
    }
}
