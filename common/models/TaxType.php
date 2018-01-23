<?php

namespace common\models;

/**
 * This is the model class for table "tax".
 *
 * @property int $id
 * @property int $province_id
 * @property float $tax_rate
 * @property string $since
 */
class TaxType extends \yii\db\ActiveRecord
{
    const HST = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tax_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'compounded', 'sort_order'], 'required'],
            [['status'], 'safe'],
            [['name'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'name' => 'Name',
            'status' => 'Status',
        ];
    }

    public function getTaxCode()
    {
        return $this->hasOne(TaxCode::className(), ['tax_type_id' => 'id']);
    }
}
