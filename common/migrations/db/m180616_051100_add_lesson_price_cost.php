<?php

use yii\db\Migration;
use common\models\Lesson;

/**
 * Class m180616_051100_add_lesson_price_cost
 */
class m180616_051100_add_lesson_price_cost extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('lesson', 'programRate', $this->decimal(10, 4)->notNull());
        $this->addColumn('lesson', 'teacherRate', $this->decimal(10, 4)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180616_051100_add_lesson_price_cost cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180616_051100_add_lesson_price_cost cannot be reverted.\n";

        return false;
    }
    */
}
