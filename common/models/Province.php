<?php

namespace common\models;

/**
 * This is the model class for table "province".
 *
 * @property int $id
 * @property string $name
 * @property float $tax_rate
 * @property int $country_id
 */
class Province extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%province}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'tax_rate', 'country_id'], 'required'],
            [['tax_rate'], 'number'],
            [['country_id'], 'integer'],
            [['name'], 'string', 'max' => 16],
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
            'tax_rate' => 'Tax Rate',
            'country_id' => 'Country Name',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }
}
