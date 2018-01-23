<?php

use yii\db\Migration;

class m170709_050121_change_isroyalty extends Migration
{
    public function up()
    {
        $this->renameColumn('invoice_line_item', 'isRoyalty', 'royaltyFree');
    }

    public function down()
    {
        echo "m170709_050121_change_isroyalty cannot be reverted.\n";

        return false;
    }
}
