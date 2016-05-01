<?php

namespace common\models;

use Yii;
use \common\models\User;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $label
 * @property string $address
 * @property integer $city_id
 * @property integer $province_id
 * @property string $postal_code
 * @property integer $country_id
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['label', 'address', 'city_id', 'province_id', 'postal_code', 'country_id'], 'required'],
            [['city_id', 'province_id', 'country_id'], 'integer'],
            [['label'], 'string', 'max' => 32],
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
            'label' => 'Label',
            'address' => 'Address',
            'city_id' => 'City ID',
            'province_id' => 'Province ID',
            'postal_code' => 'Postal Code',
            'country_id' => 'Country ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
	public function getUser() {
		return $this->hasMany(User::className(), ['id' => 'user_id'])
		  ->viaTable('user_address', ['address_id' => 'id']);
	}
}
