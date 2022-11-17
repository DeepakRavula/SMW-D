<?php

use yii\db\Migration;

/**
 * Class m180613_055005_lesson_discount
 */
class m180613_055005_lesson_discount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('lesson_discount');
        if ($tableSchema == null) {
            $this->createTable('lesson_discount', [
                'id' => $this->primaryKey(),
                'lessonId' => $this->integer()->notNull(),
                'value' => $this->decimal(10,4)->null(),
                'valueType' => $this->boolean()->notNull(),
                'type' => $this->integer()->notNull()
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180613_055005_lesson_discount cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180613_055005_lesson_discount cannot be reverted.\n";

        return false;
    }
    */
}
