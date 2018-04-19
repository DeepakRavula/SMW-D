<?php

namespace common\models;

use Yii;
use common\models\Course;

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
class CourseBasicDetail extends \yii\base\Model
{
    public $programId;
    public $programRate;
    public $paymentFrequency;
    public $pfDiscount;
    public $enrolmentDiscount;
    public $duration;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programId', 'paymentFrequency', 'duration'], 'required'],
            [['programRate', 'enrolmentDiscount', 'pfDiscount'], 'safe']
        ];
    }
}
