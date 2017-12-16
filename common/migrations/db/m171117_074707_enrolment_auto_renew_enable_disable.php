<?php

use yii\db\Migration;

class m171117_074707_enrolment_auto_renew_enable_disable extends Migration
{
    public function up()
    {
        $this->addColumn('enrolment', 'isAutoRenew', $this->integer()->notNull()->after('type'));
    }

    public function down()
    {
        echo "m171117_074707_enrolment_auto_renew_enable_disable cannot be reverted.\n";

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
