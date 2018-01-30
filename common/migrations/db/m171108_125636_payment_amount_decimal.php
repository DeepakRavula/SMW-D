<?php

use yii\db\Migration;

class m171108_125636_payment_amount_decimal extends Migration
{
    public function up()
    {
        $this->alterColumn('payment', 'amount', $this->decimal(10, 4));
    }

    public function down()
    {
        echo "m171108_125636_payment_amount_decimal cannot be reverted.\n";

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
