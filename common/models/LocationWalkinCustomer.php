<?php

namespace common\models;

use Yii;
use phpDocumentor\Reflection\Location;

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
class LocationWalkinCustomer extends \yii\db\ActiveRecord
{
    /**
      * {@inheritdoc}
      */
    public static function tableName()
    {
        return '{{%location_walkin_customer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['locationId', 'customerId'], 'safe']
        ];
    }

    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'locationId']);
    }
}
