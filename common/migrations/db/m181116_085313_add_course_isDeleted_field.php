<?php

use yii\db\Migration;

/**
 * Class m181116_085313_add_course_isDeleted_field
 */
class m181116_085313_add_course_isDeleted_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('course', 'isDeleted', $this->boolean()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181116_085313_add_course_isDeleted_field cannot be reverted.\n";

        return false;
    }
}
