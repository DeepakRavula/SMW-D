<?php

use yii\db\Migration;

class m161008_071619_master_data extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m161008_071619_master_data cannot be reverted.\n";

        return false;
    }
}
