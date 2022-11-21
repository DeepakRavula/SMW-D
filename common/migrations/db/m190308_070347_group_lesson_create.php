<?php

use yii\db\Migration;

/**
 * Class m190308_070347_group_lesson_create
 */
class m190308_070347_group_lesson_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('group_lesson');
        if ($tableSchema == null) {
            $this->createTable('group_lesson', [
                'id' => $this->primaryKey(),
                'lessonId' => $this->integer()->notNull(),
                'enrolmentId' => $this->integer()->notNull(),
                'total' => $this->decimal(10,4)->notNull()->defaultValue(0),
                'balance' => $this->decimal(10,4)->notNull()->defaultValue(0),
                'paidStatus' => $this->integer()->notNull(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190308_070347_group_lesson_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190308_070347_group_lesson_create cannot be reverted.\n";

        return false;
    }
    */
}
