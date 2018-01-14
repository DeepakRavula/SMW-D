<?php

namespace common\models;

/**
 * This is the model class for table "tax_status".
 *
 * @property string $id
 * @property string $name
 */
class TaxStatus extends \yii\db\ActiveRecord
{
    const STATUS_DEFAULT = 1;
    const STATUS_NO_TAX = 2;
    const STATUS_GST_ONLY = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tax_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 60],
            [['name'], 'trim'],
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

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\TaxStatusQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\TaxStatusQuery(get_called_class());
    }

    public function getTaxTypeTaxStatusAssoc()
    {
        return $this->hasOne(TaxTypeTaxStatusAssoc::className(), ['tax_status_id' => 'id']);
    }
}
