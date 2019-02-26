<?php

use yii\db\Migration;

/**
 * Class m190226_091018_add_indexing_lesson_discount
 */
class m190226_091018_add_indexing_lesson_discount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('lessonId', 'lesson_discount', 'lessonId');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190226_091018_add_indexing_lesson_discount cannot be reverted.\n";

        return false;
    }
}
