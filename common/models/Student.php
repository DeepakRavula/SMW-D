<?php

namespace common\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\models\query\StudentQuery;
use common\models\Location;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;
/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property int $customer_id
 */
class Student extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_DRAFT = 3;

    const CONSOLE_USER_ID  = 727;
    
    const EVENT_MERGE = 'merge';
    
    const TYPE_UPDATE = 'update';
    const TYPE_CREATE='create';
    const SCENARIO_MERGE = 'merge';
    const SCENARIO_CUSTOMER_MERGE = 'customer-merge';
    const GENDER_NOT_SPECIFIED = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    public $studentId;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%student}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required', 'except' => self::SCENARIO_MERGE],
            ['studentId', 'required', 'on' => self::SCENARIO_MERGE],
            [['first_name', 'last_name'], 'string', 'min' => 1, 'max' => 30],
            [['first_name', 'last_name'], 'trim'],
            [[ 'status'], 'integer'],
            [['birth_date'], 'date', 'format' => 'M d,Y', 'message' => 'Date format shoule be in M d,Y format',
                'except' => [self::SCENARIO_MERGE, self::SCENARIO_CUSTOMER_MERGE]],
            [['customer_id', 'isDeleted', 'gender', 'createdByUserId', 'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
        ];
    }

    public function setModel($model)
    {
        $this->first_name = $model->first_name;
        $this->last_name = $model->last_name;
        $this->birth_date = $model->birth_date;
        $this->gender = $model->gender;
        return $this;
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
            'audittrail' => [
                'class' => AuditTrailBehavior::className(), 
                'consoleUserId' => self::CONSOLE_USER_ID, 
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Student',
            'studentIds' => 'Students',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'birth_date' => 'Birth Date',
            'customer_id' => 'Customer Name',
            'showAllStudents' => 'Show All',
            'gender' => 'Gender'
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return LessonQuery the active query used by this AR class
     */
    public static function find()
    {
        return new StudentQuery(get_called_class());
    }

    public function getCustomerPaymentPreference()
    {
        return $this->hasOne(CustomerPaymentPreference::className(), ['userId' => 'customer_id']);
    }

    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customer_id']);
    }

    public function getCustomerLocation()
    {
        return $this->hasOne(UserLocation::className(), ['user_id' => 'customer_id']);
    }

    public function getCustomerProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'customer_id']);
    }

    public function getStudentCsv()
    {
        return $this->hasOne(StudentCsv::className(), ['studentId' => 'id']);
    }
    
    public function getEnrolments()
    {
        return $this->hasMany(Enrolment::className(), ['studentId' => 'id'])
            ->onCondition(['enrolment.isDeleted' => false, 'enrolment.isConfirmed' => true]);
    }

    public function getOneEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['studentId' => 'id']);
    }

    public function getFirstPrivateCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'courseId'])
            ->via('enrolments')
            ->privateProgram()
            ->onCondition(['course.isConfirmed' => true]);
    }

    public function getExamResults()
    {
        return $this->hasMany(ExamResult::className(), ['studentId' => 'id']);
    }

    public function getNotes()
    {
        return $this->hasMany(Note::className(), ['instanceId' => 'id'])
            ->onCondition(['instanceType' => Note::INSTANCE_TYPE_STUDENT]);
    }

    public function getCourse()
    {
        return $this->hasMany(Course::className(), ['id' => 'courseId'])
            ->viaTable('enrolment', ['studentId' => 'id']);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['courseId' => 'courseId'])
            ->viaTable('enrolment', ['studentId' => 'id']);
    }

    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'courseId'])
            ->viaTable('enrolment', ['studentId' => 'id']);
    }

    public function getFullName()
    {
        if ($this->first_name || $this->last_name) {
            return implode(' ', [$this->first_name, $this->last_name]);
        }

        return null;
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function beforeSave($insert)
    {
        if (!empty($this->birth_date)) {
            $birthDate = new \DateTime($this->birth_date);
            $this->birth_date = $birthDate->format('Y-m-d');
        }
        if ($insert) {
            if (empty($this->isDeleted)) {
                $this->isDeleted = false;
            }
        }

        return parent::beforeSave($insert);
    }

    public function isChangeBirthDate($changedAttributes)
    {
        return isset($changedAttributes['birth_date']) && new \DateTime($this->birth_date) != new \DateTime($changedAttributes['birth_date']);
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert && $this->isChangeBirthDate($changedAttributes)) {
            $this->trigger(self::EVENT_AFTER_UPDATE);
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function getStudentIdentity()
    {
        if ($this->getFullname()) {
            return $this->getFullname();
        }

        return $this->getFullName();
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
            self::STATUS_INACTIVE => Yii::t('common', 'Inactive'),
        ];
    }

    public function setStatus()
    {
        $studentStatus = Student::STATUS_INACTIVE;
        $customerStatus = USER::STATUS_NOT_ACTIVE;
        foreach ($this->enrolments as $enrolment) {
            if (!$enrolment->isActive()) {
                $studentStatus = Student::STATUS_INACTIVE;
                $customerStatus = USER::STATUS_NOT_ACTIVE;
            } else {
                $studentStatus = Student::STATUS_ACTIVE;
                $customerStatus = USER::STATUS_ACTIVE;
                break;
            }
        }
        $this->updateAttributes([
            'status' => $studentStatus,
            'isDeleted' => false
        ]);
        $this->customer->updateAttributes([
            'status' => $customerStatus,
            'isDeleted' => false
        ]);
        return true;
    }

    public function isDraft()
    {
        return (int) $this->status === (int) self::STATUS_DRAFT;
    }

    public static function count()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $students = self::find()
            ->location($locationId)
            ->notDeleted()
            ->statusActive()
            ->all();
        return count($students);
    }
    
    public function getGenderName()
    {
        $gender = null;
        switch ($this->gender) {
            case self::GENDER_NOT_SPECIFIED:
                $gender = 'Not Specified';
            break;
            case self::GENDER_MALE:
                $gender = 'Male';
            break;
            case self::GENDER_FEMALE:
                $gender = 'Female';
            break;
        }
        return $gender;
    }

    public function getStudentStatus() 
    {
        $status = null;
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                $status = 'Active';
            break;
            case self::STATUS_INACTIVE:
                $status = 'InActive';
            break;
        }
        return $status;
    }
}
