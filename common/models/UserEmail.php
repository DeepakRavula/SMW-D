<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_email".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $email
 * @property string $labelId
 * @property integer $isPrimary
 */
class UserEmail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_email';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'labelId', 'isPrimary'], 'required'],
            [['userId', 'isPrimary', 'labelId'], 'integer'],
            [['email'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'email' => 'Email',
            'labelId' => 'Label',
            'isPrimary' => 'Is Primary',
        ];
    }
    
    public function getLabel()
    {
        return $this->hasOne(Label::className(), ['id' => 'labelId']);
    }
}
