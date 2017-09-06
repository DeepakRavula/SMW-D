<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_line_item_discount".
 *
 * @property string $id
 * @property string $invoiceLineItemId
 * @property string $value
 * @property integer $valueType
 * @property integer $type
 */
class InvoiceLineItemDiscount extends \yii\db\ActiveRecord
{
    const VALUE_TYPE_PERCENTAGE = 0;
    const VALUE_TYPE_DOLOR      = 1;

    const TYPE_CUSTOMER = 1;
    const TYPE_ENROLMENT_PAYMENT_FREQUENCY = 2;
    const TYPE_MULTIPLE_ENROLMENT = 3;
    const TYPE_LINE_ITEM = 4;

    const SCENARIO_ON_INVOICE = 'invoice';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_line_item_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceLineItemId', 'valueType', 'type'], 'required'],
            ['value', 'safe', 'on' => self::SCENARIO_ON_INVOICE],
            [['invoiceLineItemId', 'valueType', 'type'], 'integer'],
            [['value'], 'number', 'min' => 0],
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
     * @return \common\models\query\InvoiceLineItemDiscountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\InvoiceLineItemDiscountQuery(get_called_class());
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->via('invoiceLineItem');
    }

    public function getInvoiceLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->invoiceLineItem->save();
        return parent::afterSave($insert, $changedAttributes);
    }
}
