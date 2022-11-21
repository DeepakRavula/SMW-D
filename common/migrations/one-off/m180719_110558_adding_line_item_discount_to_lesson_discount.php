<?php

use yii\db\Migration;
use common\models\InvoiceLineItem;
use common\models\discount\LessonDiscount;

/**
 * Class m180719_110558_adding_line_item_discount_to_lesson_discount
 */
class m180719_110558_adding_line_item_discount_to_lesson_discount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationId = [14, 15];
        $lineItems = InvoiceLineItem::find()
            ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($locationId) {
                $query->andWhere(['invoice.location_id' => $locationId])
                ->notDeleted();
            }])
            ->lessonItem()
            ->all();
        foreach ($lineItems as $lineItem) {
            if($lineItem->lesson){           
            if (!$lineItem->isGroupLesson()) {
                if ($lineItem->hasLineItemDiscount()) {
                    $discount = new LessonDiscount();
                    $discount->lessonId = $lineItem->lesson->id;
                    if ($lineItem->lesson->hasLineItemDiscount()) {
                        $discount = $lineItem->lesson->lineItemDiscount;
                    }
                    $discount->value = $lineItem->lineItemDiscount->value;
                    $discount->valueType = $lineItem->lineItemDiscount->valueType;
                    $discount->type = $lineItem->lineItemDiscount->type;
                    $discount->save();
                }
                if ($lineItem->hasMultiEnrolmentDiscount()) {
                    $discount = new LessonDiscount();
                    $discount->lessonId = $lineItem->lesson->id;
                    if ($lineItem->lesson->hasMultiEnrolmentDiscount()) {
                        $discount = $lineItem->lesson->multiEnrolmentDiscount;
                    }
                    $discount->value = $lineItem->multiEnrolmentDiscount->value;
                    $discount->valueType = $lineItem->multiEnrolmentDiscount->valueType;
                    $discount->type = $lineItem->multiEnrolmentDiscount->type;
                    $discount->save();
                }
                if ($lineItem->hasCustomerDiscount()) {
                    $discount = new LessonDiscount();
                    $discount->lessonId = $lineItem->lesson->id;
                    if ($lineItem->lesson->hasCustomerDiscount()) {
                        $discount = $lineItem->lesson->customerDiscount;
                    }
                    $discount->value = $lineItem->customerDiscount->value;
                    $discount->valueType = $lineItem->customerDiscount->valueType;
                    $discount->type = $lineItem->customerDiscount->type;
                    $discount->save();
                }
                if ($lineItem->hasEnrolmentPaymentFrequencyDiscount()) {
                    $discount = new LessonDiscount();
                    $discount->lessonId = $lineItem->lesson->id;
                    if ($lineItem->lesson->hasEnrolmentPaymentFrequencyDiscount()) {
                        $discount = $lineItem->lesson->enrolmentPaymentFrequencyDiscount;
                    }
                    $discount->value = $lineItem->enrolmentPaymentFrequencyDiscount->value;
                    $discount->valueType = $lineItem->enrolmentPaymentFrequencyDiscount->valueType;
                    $discount->type = $lineItem->enrolmentPaymentFrequencyDiscount->type;
                    $discount->save();
                }
            }
        }
    }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180719_110558_adding_line_item_discount_to_lesson_discount cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180719_110558_adding_line_item_discount_to_lesson_discount cannot be reverted.\n";

        return false;
    }
    */
}
