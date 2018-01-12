<?php

use yii\db\Migration;

class m180108_164038_create_pin_hash extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'pin_hash', $this->char(128)->null()->after('password_hash'));
    }

    public function down()
    {
        echo "m180108_164038_create_pin_hash cannot be reverted.\n";

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
