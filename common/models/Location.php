<?php

namespace common\models;

use yii\behaviors\SluggableBehavior;
use common\models\LocationDebt;
/**
 * This is the model class for table "location".
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property int $city_id
 * @property int $province_id
 * @property string $postal_code
 * @property int $country_id
 * @property string $from_time
 * @property string $to_time
 */
class Location extends \yii\db\ActiveRecord
{
	public $royaltyValue;
	public $advertisementValue;
	
    /**
      * {@inheritdoc}
      */
     public function behaviors()
     {
         return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                //'slugAttribute' => slug,
            ],
        ];
     }

    public static function tableName()
    {
        return '{{%location}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'address', 'phone_number', 'city_id', 'province_id', 'postal_code'], 'required'],
            [['slug'], 'safe'],
			[['royaltyValue', 'advertisementValue'], 'number'],
            [['city_id', 'province_id', 'country_id'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['address'], 'string', 'max' => 64],
            [['postal_code'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'address' => 'Address',
            'phone_number' => 'Phone Number',
            'city_id' => 'City',
            'province_id' => 'Province',
            'postal_code' => 'Postal Code',
            'country_id' => 'Country',
            'slug' => 'Slug',
			'royaltyValue' => 'Royalty (%)',
			'advertisementValue' => 'Advertisement (%)',
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

	public function getRoyalty()
    {
        return $this->hasOne(LocationDebt::className(), ['locationId' => 'id'])
			->onCondition(['location_debt.type' => LocationDebt::TYPE_ROYALTY]);
    }

	public function getAdvertisement()
    {
        return $this->hasOne(LocationDebt::className(), ['locationId' => 'id'])
			->onCondition(['location_debt.type' => LocationDebt::TYPE_ADVERTISEMENT]);
    }

    public function getLocationAvailabilities()
    {
        return $this->hasMany(LocationAvailability::className(), ['locationId' => 'id']);
    }

    public function getUserLocations()
    {
        return $this->hasMany(UserLocation::className(), ['location_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->country_id = 1;
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $model = new LocationAvailability();
            $model->locationId = $this->id;
            $model->fromTime   = LocationAvailability::DEFAULT_FROM_TIME;
            $model->toTime     = LocationAvailability::DEFAULT_TO_TIME;
            for ( $day = 1; $day < 8; $day ++ ) {
                $model->id          = null;
                $model->isNewRecord = true;
                $model->day         = $day;
                $model->save();
            }
			$locationDebt = new LocationDebt();
			$locationDebt->locationId = $this->id;
			$locationDebt->type = LocationDebt::TYPE_ROYALTY;
			$locationDebt->value = $this->royaltyValue;
			$locationDebt->save();
			
			$locationDebt->id = null;
			$locationDebt->isNewRecord = true;
			$locationDebt->type = LocationDebt::TYPE_ADVERTISEMENT;
			$locationDebt->value = $this->advertisementValue;
			$locationDebt->save();
        }
        return parent::afterSave($insert, $changedAttributes);
    }
}
