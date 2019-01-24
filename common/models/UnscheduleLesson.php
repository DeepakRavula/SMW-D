<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\models\Lesson;
use common\components\validators\lesson\conflict\ClassroomValidator;

/**
 * This is the model class for table "course".
 *
 * @property string $id
 * @property string $programId
 * @property string $teacherId
 * @property string $locationId
 * @property string $day
 * @property string $fromTime
 * @property string $startDate
 * @property string $endDate
 */
class UnscheduleLesson extends Model
{
    const SCENARIO_EDIT_CLASSROOM = 'classroom-edit';
    const SCENARIO_BULK_UNSCHEDULE = 'bulk-unschedule';

    public $lessonIds;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonIds'], 'validateOnInvoiced', 'on' => [self::SCENARIO_BULK_UNSCHEDULE]],
        ];
    }
    public function validateOnInvoiced($attribute)
    {
       foreach($this->lessonIds as $lessonId) {
          $lesson = Lesson::findOne($lessonId);
          if ($lesson->hasInvoice()) {
            $this->addError($attribute, "One of the selected lessons is invoiced. Invoiced lessons can't be edited.");
        }
      }
    }
  
}
