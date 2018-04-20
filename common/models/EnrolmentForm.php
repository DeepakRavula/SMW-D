<?php

namespace common\models;

use Yii;
use yii\base\Model;

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
class EnrolmentForm extends Model
{
    const SCENARIO_BASIC = 'enrollment-basic';
    const SCENARIO_DETAILED = 'enrolment-detail';
    
    public $programId;
    public $teacherId;
    public $day;
    public $fromTime;
    public $startDate;
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
            [['programId', 'paymentFrequency', 'duration', 'startDate', 'programRate'],
                'required', 'on' => self::SCENARIO_BASIC],
            [['programId', 'paymentFrequency', 'duration', 'startDate', 'programRate'],
                'safe', 'on' => self::SCENARIO_DETAILED],
            [['enrolmentDiscount', 'pfDiscount'], 'safe'],
            [['fromTime', 'day', 'teacherId'], 'safe', 'on' => self::SCENARIO_BASIC],
            [['fromTime', 'day', 'teacherId'], 'required', 'on' => self::SCENARIO_DETAILED]
        ];
    }
}
