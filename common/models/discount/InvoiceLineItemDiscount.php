<?php

namespace common\models\discount;

use common\models\Invoice;
use common\models\InvoiceLineItem;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

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
    const VALUE_TYPE_PERCENTAGE = 1;
    const VALUE_TYPE_DOLLAR      = 0;

    const FULL_DISCOUNT = 100.00;
    const TYPE_CUSTOMER = 1;
    const TYPE_ENROLMENT_PAYMENT_FREQUENCY = 2;
    const TYPE_MULTIPLE_ENROLMENT = 3;
    const TYPE_LINE_ITEM = 4;

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

    public function behaviors()
    {
        return [
            'audittrail'=>[
                'class'=>AuditTrailBehavior::className(), 
                'consoleUserId'=>1, 
                'attributeOutput'=>[
                    'last_checked'=>'datetime',
                ],
            ],
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

    public function isLineItemDiscount()
    {
        return (int) $this->type === (int) self::TYPE_LINE_ITEM;
    }

    public function isMultiEnrolmentDiscount()
    {
        return (int) $this->type === (int) self::TYPE_MULTIPLE_ENROLMENT;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->invoiceLineItem->save();
        return parent::afterSave($insert, $changedAttributes);
    }
}
