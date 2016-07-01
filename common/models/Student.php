<?php

namespace common\models;

use Yii;

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
	public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customer_id']);
    }
   
	public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['student_id' => 'id']);
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

	public static function timeRange($start, $end, $by='30 mins') { 

		$start_time = strtotime($start); 
		$end_time   = strtotime($end); 

		$current    = time(); 
		$add_time   = strtotime('+'.$by, $current); 
		$diff       = $add_time-$current; 

		$times = array(); 
		while ($start_time < $end_time) { 
			$times[] = date('g:i A', $start_time);
			$start_time += $diff; 
		} 
		$times[] = date('g:i A', $start_time);
		return $times; 
	}

	public static function timeRange2($start, $end, $by='30 mins') { 

		$start_time = strtotime($start); 
		$end_time   = strtotime($end); 

		$current    = time(); 
		$add_time   = strtotime('+'.$by, $current); 
		$diff       = $add_time-$current; 

		$times = array(); 
		while ($start_time < $end_time) { 
			$times[] = date('g:i', $start_time);
			$start_time += $diff; 
		} 
		$times[] = date('g:i', $start_time);
		return $times; 
	}
	public function getStudentIdentity()
	{
        if ( $this->getFullname()) {
            return $this->getFullname();
        }

        return $this->getFullName();
    }
}
