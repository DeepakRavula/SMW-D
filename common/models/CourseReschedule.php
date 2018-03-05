<?php

namespace common\models;

use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Lesson;
use Yii;
use Carbon\Carbon;
use common\models\CourseGroup;

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
class CourseReschedule extends Course
{
    public $rescheduleBeginDate;
    public $rescheduleEndDate;
    public $duration;
    public $dayTime;
    public $teacherId;

    public function setModel($model)
    {
        $this->duration = $model->duration;
        $this->teacherId = $model->teacherId;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dayTime', 'teacherId', 'duration', 'rescheduleEndDate', 'rescheduleBeginDate'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'duration' => 'Duration',
            'teacherId' => 'Teacher',
            'rescheduleBeginDate' => 'Reschedule start',
            'rescheduleEndDate' => 'Reschedule End',
            'dayTime' => 'Day & Time'
        ];
    }
}
