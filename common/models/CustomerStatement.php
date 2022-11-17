<?php

namespace common\models;

use Yii;
use yii\base\Model;



class CustomerStatement extends Model
{


    public $userId;
    public $loggedUserId;

    const EVENT_MAIL = 'mail';
    const EVENT_PRINT = 'print';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'loggedUserId'], 'safe'],
        ];
    }



}
