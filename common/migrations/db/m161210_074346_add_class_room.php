<?php

use yii\db\Migration;

class m161210_074346_add_class_room extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m161210_074346_add_class_room cannot be reverted.\n";

        return false;
    }
}
