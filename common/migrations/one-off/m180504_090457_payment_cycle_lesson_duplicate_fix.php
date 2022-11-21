<?php

use yii\db\Migration;

/**
 * Class m180504_090457_payment_cycle_lesson_duplicate_fix
 */
class m180504_090457_payment_cycle_lesson_duplicate_fix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $pcls = \common\models\PaymentCycleLesson::find()
            ->joinWith(['paymentCycle' => function ($query) {
                $query->andWhere(['payment_cycle.isDeleted' => true]);
            }])
            ->andWhere(['payment_cycle_lesson.isDeleted' => false])
            ->all();
        foreach ($pcls as $pcl) {
            $pcl->delete();
        }
        $pcs = \common\models\PaymentCycle::find()
            ->notDeleted()
            ->joinWith(['paymentCycleLesson' => function ($query) {
                $query->andWhere(['payment_cycle_lesson.id' => null]);
            }])
            ->all();
        foreach ($pcs as $pc) {
            $pc->createPaymentCycleLesson();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180504_090457_payment_cycle_lesson_duplicate_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180504_090457_payment_cycle_lesson_duplicate_fix cannot be reverted.\n";

        return false;
    }
    */
}
