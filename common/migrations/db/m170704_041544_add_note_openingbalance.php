<?php

use yii\db\Migration;

class m170704_041544_add_note_openingbalance extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m170704_041544_add_note_openingbalance cannot be reverted.\n";

        return false;
    }
}
