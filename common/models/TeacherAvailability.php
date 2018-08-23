<?php

namespace common\models;

use Yii;
use common\models\query\TeacherAvailabilityQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "teacher_availability_day".
 *
 * @property string $id
 * @property int $teacher_location_id
 * @property int $day
 * @property string $from_time
 * @property string $to_time
 */
class TeacherAvailability extends \yii\db\ActiveRecord
{
    public $name;
    public $userName;

    const EVENT_CREATE = 'event-create';
    const EVENT_UPDATE = 'event-update';
    const EVENT_DELETE = 'event-delete';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teacher_availability_day';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['day'], 'integer'],
            [['teacher_location_id', 'isDeleted', 'createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_location_id' => 'Teacher Location',
            'day' => 'Day',
            'from_time' => 'From Time',
            'to_time' => 'To Time',
        ];
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId'
            ],
        ];
    }

    public static function find()
    {
        return new TeacherAvailabilityQuery(get_called_class());
    }

    
    public static function getWeekdaysList()
    {
        return [
        1   =>  'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday'
        ];
    }

    public function IsAvailableAtLocation($teacher_location_id)
    {
        return $this->find()
                ->andWhere(['teacher_location_id' => $teacher_location_id])
                ->exists();
    }

    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
            ->viaTable('user_location', ['id' => 'teacher_location_id']);
    }

    public function getUserLocation()
    {
        return $this->hasOne(UserLocation::className(), ['id' => 'teacher_location_id']);
    }

    public function getTeacherRoom()
    {
        return $this->hasOne(TeacherRoom::className(), ['teacherAvailabilityId' => 'id']);
    }
    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            $this->trigger(TeacherAvailability::EVENT_UPDATE);
        }
        $this->trigger(TeacherAvailability::EVENT_CREATE);
    }

    public function beforeSave($insert)
    {   
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }
}
