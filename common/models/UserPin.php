<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_pin".
 *
 * @property integer $id
 * @property integer $pin
 * @property integer $userId
 */
class UserPin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_pin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pin', 'userId'], 'required'],
            [['pin', 'userId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pin' => 'Pin',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\UserPinQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\UserPinQuery(get_called_class());
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
    
    public function getUserLocation()
    {
        return $this->hasOne(UserLocation::className(), ['user_id' => 'userId']);
    }
    
    public function getPrimaryContact()
    {
        return $this->hasMany(UserContact::className(), ['userId' => 'userId'])
			->onCondition(['user_contact.isPrimary' => true]);
    } 
    
    public function getUserEmail()
    {
        return $this->hasOne(UserLocation::className(), ['user_id' => 'userId']);
    }
    
    public function getPrimaryEmail()
    {
		return $this->hasOne(UserEmail::className(), ['userContactId' => 'id'])
			->via('primaryContact');
    }
}
