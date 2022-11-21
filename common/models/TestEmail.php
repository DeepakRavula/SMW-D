<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "email-object".
 *
 * @property string $id
 * @property string $name
 */
class TestEmail extends \yii\db\ActiveRecord
{   
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test_email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email'], 'required'],
            [['email'], 'trim'],
            [['updatedByUserId', 'updatedOn',], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'updatedByAttribute' => 'updatedByUserId'
            ],
        ];
    }
}
