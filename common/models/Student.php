<?php

namespace common\models;

use Yii;
use common\models\timelineEvent\TimelineEventStudent;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\models\query\StudentQuery;

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

	const EVENT_UPDATE = 'update';
    const SCENARIO_MERGE = 'merge';
    const SCENARIO_CUSTOMER_MERGE = 'customer-merge';

    public $vacationId;
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
            [[ 'status'], 'integer'],
            [['birth_date'], 'date', 'format' => 'php:d-m-Y', 'message' => 'Date format shoule be in d-m-Y format', 
				'except' => [self::SCENARIO_MERGE, self::SCENARIO_CUSTOMER_MERGE]],
            [['customer_id', 'isDeleted'], 'safe'],
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
            ]
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

    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customer_id']);
    }

    public function getCustomerProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'customer_id']);
    }

	public function getStudentCsv()
    {
        return $this->hasOne(StudentCsv::className(), ['studentId' => 'id']);
    }
	
    public function getEnrolment()
    {
        return $this->hasMany(Enrolment::className(), ['studentId' => 'id']);
    }

    public function getFirstPrivateCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'courseId'])
            ->via('enrolment')
            ->privateProgram()
            ->onCondition(['course.isConfirmed' => true]);
    }

    public function getExamResults()
    {
        return $this->hasMany(ExamResult::className(), ['studentId' => 'id']);
    }

    public function getLogs()
    {
        return $this->hasMany(TimelineEventStudent::className(), ['studentId' => 'id']);
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

    public function beforeSave($insert)
    {
		if (!empty($this->birth_date)) {
			$birthDate = new \DateTime($this->birth_date);
			$this->birth_date = $birthDate->format('Y-m-d');
		}
		if($insert) {
            $this->isDeleted = false;
		}

        return parent::beforeSave($insert);
    }

	public function isChangeBirthDate($changedAttributes) {
		return isset($changedAttributes['birth_date']) && new \DateTime($this->birth_date) != new \DateTime($changedAttributes['birth_date']);	
	}
	
	public function afterSave($insert, $changedAttributes) {
		if(!$insert && $this->isChangeBirthDate($changedAttributes)) {
			$this->trigger(self::EVENT_UPDATE);
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
	public static function count()
    {
          $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
          $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
            return self::find()
			->location($locationId)
			->notDeleted()
                        ->enrolled($currentDate)
                        ->active()
                        ->groupBy('student.id')
			->count();
    }
}
