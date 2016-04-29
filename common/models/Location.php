<?php

namespace common\models;

use Yii;

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
class Location extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%location}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'address' => 'Address',
            'city_id' => 'City ID',
            'province_id' => 'Province ID',
            'postal_code' => 'Postal Code',
            'country_id' => 'Country ID',
        ];
    }
}
