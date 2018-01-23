<?php

use yii\db\Migration;

class m161108_053428_change_tax_rate_field extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m161108_053428_change_tax_rate_field cannot be reverted.\n";

        return false;
    }
}
