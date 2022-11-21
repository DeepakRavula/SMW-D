<?php

use yii\db\Migration;

class m180209_094744_add_type_field_in_location_availability extends Migration
{
    public function up()
    {
        $this->addColumn('location_availability', 'type', 'INT NOT NULL AFTER day');
    }

    public function down()
    {
        echo "m180209_094744_add_type_field_in_location_availability cannot be reverted.\n";

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
