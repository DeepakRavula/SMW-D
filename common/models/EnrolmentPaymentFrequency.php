<?php

namespace common\models;

use Yii;
use yii\base\Model;

class EnrolmentPaymentFrequency extends Model
{ 
    public $enrolmentId;
    public $paymentFrequencyId;
        
    public function rules()
    {
        return [
            [['enrolmentId', 'paymentFrequencyId'], 'safe' ],
        ];
    }
}