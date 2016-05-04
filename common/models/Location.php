<?php

namespace common\models;

use Yii;
use common\models\City;
use common\models\Country;
use common\models\Province;

/**
 * This is the model class for table "location".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property integer $city_id
 * @property integer $province_id
 * @property string $postal_code
 * @property integer $country_id
 */
class Location extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%location}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['name', 'address', 'city_id', 'province_id', 'postal_code', 'country_id'], 'required'],
			[['city_id', 'province_id', 'country_id'], 'integer'],
			[['name'], 'string', 'max' => 32],
			[['address'], 'string', 'max' => 64],
			[['postal_code'], 'string', 'max' => 16],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'name' => 'Name',
			'address' => 'Address',
			'city_id' => 'City',
			'province_id' => 'Province',
			'postal_code' => 'Postal Code',
			'country_id' => 'Country',
		];
	}


	public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

	public function getCity()
    {
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

	public function getProvince()
    {
        return $this->hasOne(Province::className(), ['id' => 'province_id']);
    }

	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
			$this->country_id = 1; // 
			return true;
		} else {
			return false;
		}
	}
}
