<?php

namespace common\models;

use Yii;
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

	public $vacationId;
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
            [['first_name', 'last_name'], 'required'],
            [['first_name', 'last_name'], 'string', 'min' => 2, 'max' => 30],
            [[ 'status'], 'integer'],
            [['birth_date'], 'date', 'format' => 'php:d-m-Y'],
            [['customer_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'birth_date' => 'Birth Date',
            'customer_id' => 'Customer Name',
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
			$birthDate = \DateTime::createFromFormat('d-m-Y', $this->birth_date);
			$this->birth_date = $birthDate->format('Y-m-d');
		}
		if($insert) {
        	$this->status = self::STATUS_ACTIVE;
		}

        return parent::beforeSave($insert);
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
}
