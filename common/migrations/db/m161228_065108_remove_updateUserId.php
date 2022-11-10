<?php

use yii\db\Migration;

class m161228_065108_remove_updateUserId extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m161228_065108_remove_updateUserId cannot be reverted.\n";

        return false;
    }
}
