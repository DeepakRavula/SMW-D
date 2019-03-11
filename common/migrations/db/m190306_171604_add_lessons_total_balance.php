<?php

use yii\db\Migration;
use common\models\Lesson;

/**
 * Class m190306_171604_add_lessons_total_balance
 */
class m190306_171604_add_lessons_total_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('private_lesson', 'total', $this->decimal(10, 4)->notNull()->defaultValue(0.00));
        $this->addColumn('private_lesson', 'balance', $this->decimal(10, 4)->notNull()->defaultValue(0.00));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190306_171604_add_lessons_total_balance cannot be reverted.\n";

        return false;
    }
}
