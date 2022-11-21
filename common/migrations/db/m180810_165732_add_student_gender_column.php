<?php

use yii\db\Migration;

/**
 * Class m180810_165732_add_student_gender_column
 */
class m180810_165732_add_student_gender_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('student', 'gender', $this->string()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180810_165732_add_student_gender_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180810_165732_add_student_gender_column cannot be reverted.\n";

        return false;
    }
    */
}
