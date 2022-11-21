<?php

namespace common\models;

/**
 * This is the model class for table "user_address".
 *
 * @property int $id
 * @property int $user_id
 * @property int $address_id
 */
class UserLocation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_location';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'location_id'], 'required'],
            [['user_id', 'location_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'location_id' => 'Location ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherAvailability()
    {
        return $this->hasOne(TeacherAvailability::className(), ['teacher_location_id' => 'id'])
            ->onCondition(['teacher_availability_day.isDeleted' => false]);
    }

    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }
    
    public function getTeacherAvailabilities()
    {
        return $this->hasMany(TeacherAvailability::className(), ['teacher_location_id' => 'id'])
            ->andWhere(['teacher_availability_day.isDeleted' => false]);
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'user_id']);
    }

    public function getQualification()
    {
        return $this->hasOne(Qualification::className(), ['teacher_id' => 'user_id']);
    }

    public function getQualifications()
    {
        return $this->hasMany(Qualification::className(), ['teacher_id' => 'user_id']);
    }

    public function beforeDelete()
    {
        foreach ($this->teacherAvailabilities as $teacherAvailability) {
            $teacherAvailability->delete();
        }
        return parent::beforeDelete();
    }
}
