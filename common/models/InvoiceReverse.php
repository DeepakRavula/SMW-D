<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property int $invoiceId
 * @property int $reversedInvoiceId
*/
class InvoiceReverse extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'invoice_reverse';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['invoiceId', 'reversedInvoiceId'], 'required'],
            [['invoiceId', 'reversedInvoiceId'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoiceId' => 'Invoice ID',
            'reverseInvoiceId' => 'Reverse Invoice ID',
        ];
    }
}
