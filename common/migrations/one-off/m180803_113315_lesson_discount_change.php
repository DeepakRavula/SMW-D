<?php

use yii\db\Migration;
use common\models\discount\LessonDiscount;

/**
 * Class m180803_113315_lesson_discount_change
 */
class m180803_113315_lesson_discount_change extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $lessonDiscount = LessonDiscount::findOne(['8611']);
        $lessonDiscount->updateAttributes(['value' => 2.5650, 'valueType' => 0,'type' => 4]);
         }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180803_113315_lesson_discount_change cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180803_113315_lesson_discount_change cannot be reverted.\n";

        return false;
    }
    */
}
