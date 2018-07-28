<?php

use yii\db\Migration;

/**
 * Class m180728_105251_apply_primary_key_for_lesson_hierarchy
 */
class m180728_105251_apply_primary_key_for_lesson_hierarchy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addPrimaryKey('lessonId_primarykey','lesson_hierarchy', 'lessonId');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180728_105251_apply_primary_key_for_lesson_hierarchy cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180728_105251_apply_primary_key_for_lesson_hierarchy cannot be reverted.\n";

        return false;
    }
    */
}
