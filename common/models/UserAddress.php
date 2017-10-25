<?php

namespace common\models;

/**
 * This is the model class for table "user_address".
 *
 * @property int $id
 * @property int $user_id
 * @property int $address_id
 */
class UserAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
			[['address', 'cityId', 'provinceId', 'countryId'], 'required'],
            [['userContactId', 'cityId', 'provinceId', 'countryId'], 'integer'],
            [['address'], 'string', 'max' => 64],
            [['postalCode'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'address_id' => 'Address ID',
        ];
    }
}
