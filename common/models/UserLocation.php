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
            ->andWhere(['not', ['teacher_availability_day.id' => null]]);
    }

    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'user_id']);
    }

    public function getQualification()
    {
        return $this->hasOne(Qualification::className(), ['teacher_id' => 'user_id']);
    }
}
