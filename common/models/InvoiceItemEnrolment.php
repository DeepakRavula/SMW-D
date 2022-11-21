<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_item_enrolment".
 *
 * @property string $id
 * @property string $invoiceLineItemId
 * @property string $enrolmentId
 */
class InvoiceItemEnrolment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_item_enrolment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceLineItemId', 'enrolmentId'], 'required'],
            [['invoiceLineItemId', 'enrolmentId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoiceLineItemId' => 'Invoice Line Item ID',
            'enrolmentId' => 'Enrolment ID',
        ];
    }

    public function getLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId']);
    }

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId']);
    }
}
