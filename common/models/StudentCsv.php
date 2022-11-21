<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "student_csv".
 *
 * @property string $id
 * @property string $studentId
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $address
 * @property string $city
 * @property string $province
 * @property string $postalCode
 * @property string $country
 * @property string $homeTel
 * @property string $otherTel
 * @property string $birthDate
 * @property string $billingFirstName
 * @property string $billingLastName
 * @property string $billingEmail
 * @property string $billingAddress
 * @property string $billingCity
 * @property string $billingProvince
 * @property string $billingPostalCode
 * @property string $billingCountry
 * @property string $billingHomeTel
 * @property string $billingOtherTel
 * @property string $billingWorkTel
 * @property integer $billingWorkTelExt
 */
class StudentCsv extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student_csv';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studentId', 'firstName', 'lastName'], 'required'],
            [['studentId', 'billingWorkTelExt'], 'integer'],
            [['birthDate', 'openingBalance'], 'safe'],
            [['firstName', 'lastName'], 'string', 'max' => 25],
            [['email'], 'string', 'max' => 50],
            [['address', 'billingAddress'], 'string', 'max' => 60],
            [['city', 'province', 'postalCode', 'country', 'billingFirstName', 'billingLastName', 'billingEmail', 'billingCity', 'billingProvince', 'billingPostalCode', 'billingCountry'], 'string', 'max' => 40],
            [['homeTel', 'otherTel', 'billingHomeTel', 'billingOtherTel', 'billingWorkTel'], 'string', 'max' => 12],
            [['city', 'province', 'postalCode', 'country', 'billingFirstName', 'billingLastName', 'billingEmail', 'billingCity', 'billingProvince', 'billingPostalCode', 'billingCountry','homeTel', 'otherTel', 'billingHomeTel', 'billingOtherTel', 'billingWorkTel','firstName', 'lastName','address', 'billingAddress',], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'studentId' => 'Student ID',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'email' => 'Email',
            'address' => 'Address',
            'city' => 'City',
            'province' => 'Province',
            'postalCode' => 'Postal Code',
            'country' => 'Country',
            'homeTel' => 'Home Tel',
            'otherTel' => 'Other Tel',
            'birthDate' => 'Birth Date',
            'billingFirstName' => 'Billing First Name',
            'billingLastName' => 'Billing Last Name',
            'billingEmail' => 'Billing Email',
            'billingAddress' => 'Billing Address',
            'billingCity' => 'Billing City',
            'billingProvince' => 'Billing Province',
            'billingPostalCode' => 'Billing Postal Code',
            'billingCountry' => 'Billing Country',
            'billingHomeTel' => 'Billing Home Tel',
            'billingOtherTel' => 'Billing Other Tel',
            'billingWorkTel' => 'Billing Work Tel',
            'billingWorkTelExt' => 'Billing Work Tel Ext',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            // if (!empty($this->openingBalance) && round(abs($this->openingBalance), 2) > round(abs(0.00), 2)) {
            //     $openingBalanceModel = new OpeningBalance();
            //     $openingBalanceModel->user_id = $this->student->customer->id;
            //     $openingBalanceModel->amount = $this->openingBalance;
            //     $openingBalanceModel->isCredit = $this->openingBalance > 0 ? 0 : 1;
            //     $openingBalanceModel->addOpeningBalance();
            // }
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentId']);
    }
}
