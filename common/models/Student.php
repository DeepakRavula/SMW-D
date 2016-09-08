<?php

namespace common\models;

use Yii;
use common\models\query\StudentQuery;
use \yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "student".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property integer $customer_id
 */
class Student extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
		return '{{%student}}';
    }

	public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true
                ],
            ],
        ];
    }
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['birth_date','notes'], 'safe'],
            [['customer_id'], 'integer'],
            [['first_name', 'last_name'], 'string', 'max' => 30],
			[['birth_date'], 'date', 'format' => 'php:d-m-Y']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'birth_date' => 'Birth Date',
            'customer_id' => 'Customer Name',
            'notes' => 'Notes',
        ];
    }

	/**
     * @inheritdoc
     * @return LessonQuery the active query used by this AR class.
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
   
	public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['student_id' => 'id']);
    }

	public function getGroupEnrolments()
    {
        return $this->hasMany(GroupEnrolment::className(), ['student_id' => 'id']);
    }

	public function getGroupCourse()
    {
        return $this->hasOne(GroupCourse::className(), ['id' => 'course_id'])
		  ->viaTable('group_enrolment', ['student_id' => 'id']);
    }
	
	public function getFullName()
    {
		if ($this->first_name || $this->last_name) {
            return implode(' ', [$this->first_name, $this->last_name]);
        }
        return null;
    }

	public function beforeSave($insert) {
		if(! empty($this->birth_date)){
	        $birthDate = \DateTime::createFromFormat('d-m-Y', $this->birth_date);
    	    $this->birth_date = $birthDate->format('Y-m-d');
		}
		return parent::beforeSave($insert);
	}

	public function getStudentIdentity()
	{
        if ( $this->getFullname()) {
            return $this->getFullname();
        }

        return $this->getFullName();
    }

	public function getEnrolments()
	{
    	return $this->hasMany(Enrolment::className(), ['student_id' => 'id']);
	}

	public function getEnrolmentsCount()
	{
    	return $this->getEnrolments()->count();
	}
}
