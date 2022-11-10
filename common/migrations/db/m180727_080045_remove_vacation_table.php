<?php

use yii\db\Migration;

/**
 * Class m180727_080045_remove_vacation_table
 */
class m180727_080045_remove_vacation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('vacation');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180727_080045_remove_vacation_table cannot be reverted.\n";

        return false;
    }
}
