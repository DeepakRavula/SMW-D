<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_phone".
 *
 * @property integer $id
 * @property integer $userContactId
 * @property string $number
 * @property integer $extension
 */
class UserPhone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_phone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userContactId', 'number', 'extension'], 'required'],
            [['userContactId', 'extension'], 'integer'],
            [['number'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userContactId' => 'User Contact ID',
            'number' => 'Number',
            'extension' => 'Extension',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\UserPhoneQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserPhoneQuery(get_called_class());
    }
}
