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
    const TYPE_EMAIL = 1;
    const TYPE_PHONE = 2;
    const TYPE_ADDRESS = 3;
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
            [['userId'], 'integer'],
            [['isDeleted'], 'safe'],
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
    public function getEmail()
    {
        return $this->hasOne(UserEmail::className(), ['userContactId' => 'id']);
    }
    public function getPhone()
    {
        return $this->hasOne(UserPhone::className(), ['userContactId' => 'id']);
    }
    public function getAddress()
    {
        return $this->hasOne(UserAddress::className(), ['userContactId' => 'id']);
    }
    public function getLabel()
    {
        return $this->hasOne(Label::className(), ['id' => 'labelId']);
    }
    public function getUserLocation()
    {
        return $this->hasOne(UserLocation::className(), ['user_id' => 'userId']);
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
    
    public function makePrimary()
    {
        $this->isPrimary = true;
        return $this->save();
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }
}
