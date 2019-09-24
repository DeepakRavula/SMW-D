<?php

use yii\db\Migration;

/**
 * Class m190924_100358_add_auto_renewal_enrolment_generated_lessons
 */
class m190924_100358_add_auto_renewal_enrolment_generated_lessons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('auto_renewal_enrolment_lessons');
        if ($tableSchema == null) {
            $this->createTable('auto_renewal_enrolment_lessons', [
                'id' => $this->primaryKey(),
                'lessonId' => $this->integer()->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'updatedOn' => $this->timeStamp()->defaultValue(null),
                'createdByUserId' =>  $this->integer()->notNull(),
                'updatedByUserId' =>  $this->integer()->notNull(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190924_100358_add_auto_renewal_enrolment_generated_lessons cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190924_100358_add_auto_renewal_enrolment_generated_lessons cannot be reverted.\n";

        return false;
    }
    */
}
