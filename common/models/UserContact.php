<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_contact".
 *
 * @property integer $id
 * @property integer $userId
 * @property integer $isPrimary
 * @property integer $labelId
 */
class UserContact extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'isPrimary', 'labelId'], 'required'],
            [['userId', 'isPrimary', 'labelId'], 'integer'],
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
            'isPrimary' => 'Is Primary',
            'labelId' => 'Label ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\UserContactQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserContactQuery(get_called_class());
    }
}
