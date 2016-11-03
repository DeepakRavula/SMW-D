<?php

use yii\db\Migration;

class m161019_074346_change_totime_field extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m161019_074346_change_totime_field cannot be reverted.\n";

        return false;
    }
}
