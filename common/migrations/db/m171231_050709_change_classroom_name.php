<?php

use yii\db\Migration;

class m171231_050709_change_classroom_name extends Migration
{
    public function up()
    {
        $this->alterColumn('classroom', 'name', $this->string(250));
    }

    public function down()
    {
        echo "m171231_050709_change_classroom_name cannot be reverted.\n";

        return false;
    }
}
