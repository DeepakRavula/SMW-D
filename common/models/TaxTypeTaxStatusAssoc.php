<?php

namespace common\models;

/**
 * This is the model class for table "tax_status".
 *
 * @property string $id
 * @property string $name
 */
class TaxTypeTaxStatusAssoc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tax_type_tax_status_assoc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tax_type_id', 'tax_status_id', 'exempt'], 'required'],
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
        ];
    }

    public function getTaxType()
    {
        return $this->hasOne(TaxType::className(), ['id' => 'tax_type_id']);
    }
}
