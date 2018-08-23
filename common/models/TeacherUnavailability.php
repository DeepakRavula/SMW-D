<?php

namespace common\models;

use Yii;
use common\models\query\TeacherUnavailabilityQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "classroom_unavailability".
 *
 * @property string $id
 * @property string $classroomId
 * @property string $fromDate
 * @property string $toDate
 * @property string $reason
 */
class TeacherUnavailability extends \yii\db\ActiveRecord
{
     private $dateRange;

    public function getDateRange()
    {
        return $this->dateRange;;
    }

    public function setDateRange($value)
    {
        $this->dateRange = $value;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teacher_unavailability';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dateRange'], 'required'],
            [['teacherId'], 'integer'],
            [['fromDate', 'toDate'], 'safe'],
            [['fromTime', 'toTime', 'reason', 'createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacherId' => 'Teacher',
            'fromDate' => 'From Date',
            'toDate' => 'To Date',
            'fromTime' => 'From Time',
            'toTime' => 'To Time',
            'reason' => 'Reason'
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
        return new TeacherUnavailabilityQuery(
            get_called_class(),
            parent::find()->andWhere(['teacher_unavailability.isDeleted' => false])
        );
    }
    public function beforeSave($insert)
    {
        if (!empty($this->fromTime) || !empty($this->toTime)) {
            $this->fromTime = (new \DateTime($this->fromTime))->format('H:i:s');
            $this->toTime = (new \DateTime($this->toTime))->format('H:i:s');
        }
        list($fromDate, $toDate) = explode(' - ', $this->dateRange);
        $this->fromDate = (new \DateTime($fromDate))->format('Y-m-d');
        $this->toDate = (new \DateTime($toDate))->format('Y-m-d');
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }
}
