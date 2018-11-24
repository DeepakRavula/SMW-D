<?php

use yii\db\Migration;
use common\models\discount\LessonDiscount;

/**
 * Class m181123_093558_add_enrolment_details_to_lesson_discount
 */
class m181123_093558_add_enrolment_details_to_lesson_discount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //$this->addColumn('lesson_discount', 'enrolmentId', $this->integer());

        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $lessonDiscounts = LessonDiscount::find()
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
        echo "m181123_093558_add_enrolment_details_to_lesson_discount cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181123_093558_add_enrolment_details_to_lesson_discount cannot be reverted.\n";

        return false;
    }
    */
}
