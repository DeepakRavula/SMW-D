<?php

use yii\db\Migration;

class m161227_043928_add_note extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m161227_043928_add_note cannot be reverted.\n";

        return false;
    }
}
