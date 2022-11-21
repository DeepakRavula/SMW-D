<?php

namespace common\models;

use Yii;
use common\components\validators\lesson\conflict\TeacherUnavailabilityValidator;
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
            [['teacherId'], 'integer'],
            [['reason', 'createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
            ['toDateTime', 'validateToDateTime'],
            [['fromDateTime', 'toDateTime'], 'required'],
            [['fromDateTime'], TeacherUnavailabilityValidator::className()],
            [['toDateTime'], TeacherUnavailabilityValidator::className()],
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
            'fromDateTime' => 'From Date Time',
            'toDateTime' => 'To Date Time',
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
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }

    public function validateToDateTime($attribute) {
        $fromDateTime = (new \DateTime($this->fromDateTime))->format('Y-m-d H:i');
        $toDateTime = (new \DateTime($this->toDateTime))->format('Y-m-d H:i');
        if ($fromDateTime > $toDateTime) {
            $this->addError($attribute, "Enrolment end date must be greater than or equal to start date");
        }
    }
}
