<?php

use yii\db\Migration;
use common\models\discount\LessonDiscount;

/**
 * Class m181025_100313_enable_customer_payment_preference_richmond_hill
 */
class m181026_100319_migarte_lesson_disconut extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $lessonDiscounts = LessonDiscount::find()
            ->andWhere(['enrolmentId' => null])
            ->all();

        foreach ($lessonDiscounts as $lessonDiscount) {
            $lessonDiscount->updateAttributes(['enrolmentId' => $lessonDiscount->lesson->enrolment->id]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181025_100313_enable_customer_payment_preference_richmond_hill cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181025_100313_enable_customer_payment_preference_richmond_hill cannot be reverted.\n";

        return false;
    }
    */
}
