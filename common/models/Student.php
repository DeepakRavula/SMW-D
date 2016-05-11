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
            [['birth_date'], 'safe'],
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
        ];
    }
	public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customer_id']);
    }
    
	public function getFullName()
    {
		if ($this->first_name || $this->last_name) {
            return implode(' ', [$this->first_name, $this->last_name]);
        }
        return null;
    }

	public function beforeSave($insert) {
		$birthDate = \DateTime::createFromFormat('m-d-y', $this->birth_date);
		$this->birth_date = $birthDate->format('Y-m-d');

		return parent::beforeSave($insert);
	}
}
