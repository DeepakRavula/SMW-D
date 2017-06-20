<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_item_enrolment".
 *
 * @property string $id
 * @property string $invoiceLineItemId
 * @property string $enrolemntId
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
            [['invoiceLineItemId', 'enrolemntId'], 'required'],
            [['invoiceLineItemId', 'enrolemntId'], 'integer'],
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
            'enrolemntId' => 'Enrolemnt ID',
        ];
    }
}
