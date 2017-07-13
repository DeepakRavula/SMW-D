<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_discount".
 *
 * @property string $id
 * @property string $invoiceId
 * @property string $value
 * @property integer $valueType
 * @property integer $type
 */
class InvoiceDiscount extends \yii\db\ActiveRecord
{
    const VALUE_TYPE_PERCENTAGE = 0;
    const VALUE_TYPE_DOLOR      = 1;

    const TYPE_CUSTOMER = 1;

    const SCENARIO_EDIT = 'edit';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceId', 'valueType', 'type'], 'required'],
            ['value', 'required', 'except' => self::SCENARIO_EDIT],
            [['invoiceId', 'valueType', 'type'], 'integer'],
            [['value'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoiceId' => 'Invoice ID',
            'value' => 'Value',
            'valueType' => 'Value Type',
            'type' => 'Type',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\InvoiceDiscountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\InvoiceDiscountQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->invoice->save();
        return parent::afterSave($insert, $changedAttributes);
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoiceId']);
    }
}
