<?php

use yii\db\Migration;

class m161104_172630_add_vacation extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m161104_172630_add_vacation cannot be reverted.\n";

        return false;
    }
}
