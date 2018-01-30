<?php

use yii\db\Migration;

class m171228_151250_add_merge_activity extends Migration
{
    public function up()
    {
        $this->insert('log_activity', [
            'name' => 'merge',
        ]);
    }

    public function down()
    {
        echo "m171228_151250_add_merge_activity cannot be reverted.\n";

        return false;
    }
}
