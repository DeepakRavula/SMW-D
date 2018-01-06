<?php

namespace backend\models;

use cheatsheet\Time;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\web\ForbiddenHttpException;

/**
 * Login form.
 */
class UnlockForm extends Model
{
    public $pin;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['pin'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pin' => Yii::t('backend', 'Enter Pin'),
        ];
    }
}
