<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_cycle_lesson".
 *
 * @property string $id
 * @property string $paymentCycleId
 * @property string $lessonId
 */
class PaymentCycleLesson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_cycle_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['paymentCycleId', 'lessonId'], 'required'],
            [['paymentCycleId', 'lessonId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paymentCycleId' => 'Payment Cycle ID',
            'lessonId' => 'Lesson ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\PaymentCycleLessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\PaymentCycleLessonQuery(get_called_class());
    }

    public function getProFormaInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->via('invoiceLineItems')
                ->onCondition(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
    }

    public function getInvoiceLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
            ->viaTable('invoice_item_Payment_cycle_lesson', ['paymentCycleLessonId' => 'id'])
                ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON]);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }

    public function getPaymentCycle()
    {
        return $this->hasOne(PaymentCycle::className(), ['id' => 'paymentCycleId']);
    }
}
