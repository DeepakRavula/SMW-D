<?php

use yii\db\Migration;

class m171109_091049_enrolment_rate extends Migration
{
    public function up()
    {
        $this->addColumn('enrolment', 'programRate', $this->decimal(10, 2)->notNull()->after('studentId'));
    }

    public function down()
    {
        echo "m171109_091049_enrolment_rate cannot be reverted.\n";

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
