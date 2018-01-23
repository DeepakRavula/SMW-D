<?php

use yii\db\Migration;

class m161207_173813_calendar_event_color extends Migration
{
    public function up()
    {
        $sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m161207_173813_calendar_event_color cannot be reverted.\n";

        return false;
    }
}
