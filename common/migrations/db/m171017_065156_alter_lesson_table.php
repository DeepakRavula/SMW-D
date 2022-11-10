<?php

use yii\db\Migration;

class m171017_065156_alter_lesson_table extends Migration
{
    public function up()
    {
        $this->addColumn('lesson', 'createdByUserId', $this->integer()->notNull()->after('isExploded'));
        $this->addColumn('lesson', 'updatedByUserId', $this->integer()->notNull()->after('createdByUserId'));
    }

    public function down()
    {
        echo "m171017_065156_alter_lesson_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
