<?php

use yii\db\Migration;

/**
 * Class m181121_110043_add_classroom_isDeleted_field
 */
class m181121_110043_add_classroom_isDeleted_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('classroom', 'isDeleted', $this->boolean()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181121_110043_add_classroom_isDeleted_field cannot be reverted.\n";

        return false;
    }
}
