<?php

namespace common\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "address".
 *
 * @property int $id
 * @property string $label
 * @property string $address
 * @property int $city_id
 * @property int $province_id
 * @property string $postal_code
 * @property int $country_id
 */
class Address extends \yii\db\ActiveRecord
{
    const LABEL_HOME = 'Home';
    const LABEL_WORK = 'Work';
    const LABEL_OTHER = 'Other';
    const LABEL_BILLING = 'Billing';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['label', 'address', 'city_id', 'province_id', 'country_id'], 'required'],
            [['city_id', 'province_id', 'country_id'], 'integer'],
            [['label'], 'string', 'max' => 32],
            [['address'], 'string', 'max' => 64],
            [['postal_code'], 'string', 'max' => 16],
            [['is_primary'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label' => 'Label',
            'address' => 'Address',
            'city_id' => 'City',
            'province_id' => 'Province',
            'postal_code' => 'Postal Code',
            'country_id' => 'Country',
            'is_primary' => 'Primary Address',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])
          ->viaTable('user_address', ['address_id' => 'id']);
    }

    public function getUsers()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
          ->viaTable('user_address', ['address_id' => 'id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getProvince()
    {
        return $this->hasOne(Province::className(), ['id' => 'province_id']);
    }

    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }
    public static function findByUserId($user_id)
    {
        return static::find()
            ->join('INNER JOIN', 'user_address', 'user_address.address_id = address.id')
            ->where(['user_address.user_id' => $user_id])
            ->one();
    }

    public static function labels()
    {
        return [
            self::LABEL_HOME => Yii::t('common', 'Home'),
            self::LABEL_WORK => Yii::t('common', 'Work'),
            self::LABEL_OTHER => Yii::t('common', 'Other'),
            self::LABEL_BILLING => Yii::t('common', 'Billing'),
        ];
    }
}
