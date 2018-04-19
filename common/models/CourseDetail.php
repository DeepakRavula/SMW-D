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
class CourseDetail extends Model
{
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
            [['programId', 'paymentFrequency', 'duration', 'startDate'], 'required'],
            [['programRate', 'enrolmentDiscount', 'pfDiscount'], 'required'],
            [['fromTime', 'day', 'teacherId'], 'required']
        ];
    }

    public function setModel($model)
    {
        $this->programId = $model->programId;
        $this->programRate = $model->programRate;
        $this->paymentFrequency = $model->paymentFrequency;
        $this->duration = $model->duration;
        $this->pfDiscount = $model->pfDiscount;
        $this->enrolmentDiscount = $model->enrolmentDiscount;
        $this->startDate = $model->startDate;
        $this->day = $model->day;
        $this->fromTime = $model->fromTime;
        $this->teacherId = $model->teacherId;
        return $this;
    }
}
