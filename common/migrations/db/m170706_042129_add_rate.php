<?php

use yii\db\Migration;

class m170706_042129_add_rate extends Migration
{
    public function up()
    {
        $this->addColumn('invoice_line_item', 'rate', 'DECIMAL(10,4) UNSIGNED NULL AFTER cost');
    }

    public function down()
    {
        echo "m170706_042129_add_rate cannot be reverted.\n";

        return false;
    }
}
