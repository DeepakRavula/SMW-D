<?php

use yii\db\Migration;

class m170104_074537_add_location_availability extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m170104_074537_add_location_availability cannot be reverted.\n";

        return false;
    }
}
