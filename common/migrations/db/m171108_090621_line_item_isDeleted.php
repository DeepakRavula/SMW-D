<?php

use yii\db\Migration;

class m171108_090621_line_item_isDeleted extends Migration
{
    public function up()
    {
        $this->addColumn('invoice_line_item', 'isDeleted', $this->boolean()->after('rate'));
    }

    public function down()
    {
        echo "m171108_090621_line_item_isDeleted cannot be reverted.\n";

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
