<?php

use yii\db\Migration;

class m161226_073528_add_discount extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m161226_073528_add_discount cannot be reverted.\n";

        return false;
    }
}
