<?php

use yii\db\Migration;
use common\models\discount\EnrolmentDiscount;

class m170727_061830_update_enrolment_discount_type extends Migration
{
    public function up()
    {
        $paymentFrequencyDiscounts = EnrolmentDiscount::find()
            ->where(['type' => EnrolmentDiscount::TYPE_PAYMENT_FREQUENCY, 'discountType' => true])
            ->all();
        foreach ($paymentFrequencyDiscounts as $paymentFrequencyDiscount) {
            $paymentFrequencyDiscount->updateAttributes([
                'discountType' => 0
            ]);
        }
        $multiEnrolmentDiscounts = EnrolmentDiscount::find()
            ->where(['type' => EnrolmentDiscount::TYPE_MULTIPLE_ENROLMENT, 'discountType' => 0])
            ->all();
        foreach ($multiEnrolmentDiscounts as $multiEnrolmentDiscount) {
            $multiEnrolmentDiscount->updateAttributes([
                'discountType' => true
            ]);
        }
    }

    public function down()
    {
        echo "m170727_061830_update_enrolment_discount_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
