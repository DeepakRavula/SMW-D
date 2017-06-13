<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_item_payment_cycle_lesson".
 *
 * @property string $id
 * @property string $invoiceLineItemId
 * @property string $paymentCycleLessonId
 */
class InvoiceItemPaymentCycleLesson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_item_payment_cycle_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceLineItemId', 'paymentCycleLessonId'], 'required'],
            [['invoiceLineItemId', 'paymentCycleLessonId'], 'integer'],
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
            'paymentCycleLessonId' => 'Payment Cycle Lesson ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\InvoiceItemPaymentCycleLessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\InvoiceItemPaymentCycleLessonQuery(get_called_class());
    }
}
