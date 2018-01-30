<?php

use yii\db\Migration;

class m171208_012646_change_rate_datatype extends Migration
{
    public function up()
    {
        $this->alterColumn('qualification', 'rate', $this->decimal(10, 2)->null());
    }

    public function down()
    {
        echo "m171208_012646_change_rate_datatype cannot be reverted.\n";

        return false;
    }
}
