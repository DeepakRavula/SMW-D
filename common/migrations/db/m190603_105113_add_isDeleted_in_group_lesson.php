<?php

use yii\db\Migration;

/**
 * Class m190603_105113_add_isDeleted_in_group_lesson
 */
class m190603_105113_add_isDeleted_in_group_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'group_lesson',
            'isDeleted',
            $this->boolean()->notNull()->after('paidStatus')
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190603_105113_add_isDeleted_in_group_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190603_105113_add_isDeleted_in_group_lesson cannot be reverted.\n";

        return false;
    }
    */
}
