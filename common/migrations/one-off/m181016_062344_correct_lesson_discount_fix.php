<?php

use yii\db\Migration;
use common\models\discount\LessonDiscount;

/**
 * Class m181016_062344_correct_lesson_discount_fix
 */
class m181016_062344_correct_lesson_discount_fix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $lessonDiscounts = LessonDiscount::find()->andWhere(['AND',
        ['>', 'id', 69146],
        ['<', 'id', 69891],
    ])->all();
        foreach($lessonDiscounts as $lessonDiscount) {
            $lessonDiscount->delete();
        }
        $lessonDiscounts = LessonDiscount::find()->andWhere(['AND',
        ['>', 'id', 71340],
        ['<', 'id', 72478],
    ])->all();
        foreach($lessonDiscounts as $lessonDiscount) {
            $lessonDiscount->delete();
        }

}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181016_062344_correct_lesson_discount_fix cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181016_062344_correct_lesson_discount_fix cannot be reverted.\n";

        return false;
    }
    */
}
