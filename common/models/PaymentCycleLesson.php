<?php

namespace common\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use Yii;
use Carbon\Carbon;

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
            ['isDeleted', 'safe']
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

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
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
            ->via('invoiceItemPaymentCycleLesson')
                ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON,
                    'invoice_line_item.isDeleted' => false]);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }

    public function getPaymentCycle()
    {
        return $this->hasOne(PaymentCycle::className(), ['id' => 'paymentCycleId']);
    }

    public function getInvoiceItemPaymentCycleLessons()
    {
        return $this->hasMany(InvoiceItemPaymentCycleLesson::className(), ['paymentCycleLessonId' => 'id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $firstLessonDate = $this->paymentCycle->firstLesson->getOriginalDate();
            $dueDate = carbon::parse($firstLessonDate)->modify('first day of previous month');
            $dueDate = carbon::parse($dueDate)->modify('+ 14 day')->format('Y-m-d');
            $this->lesson->updateAttributes(['dueDate' => $dueDate]);
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function getAutoRenewalLessons()
    {
        return $this->hasOne(AutoRenewalLessons::className(), ['lessonId' => 'lessonId']);
    }
}
