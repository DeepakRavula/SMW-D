<?php

use yii\db\Migration;

/**
 * Class m181126_070125_add_course_extra_isDeleted_field
 */
class m181126_070125_add_course_extra_isDeleted_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('course_extra', 'isDeleted', $this->boolean()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181126_070125_add_course_extra_isDeleted_field cannot be reverted.\n";

        return false;
    }
}
