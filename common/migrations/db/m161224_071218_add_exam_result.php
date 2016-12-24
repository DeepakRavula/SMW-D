<?php

use yii\db\Migration;

class m161224_071218_add_exam_result extends Migration
{
    public function up()
    {
		$sql = file_get_contents(dirname(__FILE__).'/'.get_class($this).'_'.__FUNCTION__.'.sql');

        return $this->execute($sql);
    }

    public function down()
    {
        echo "m161224_071218_add_exam_result cannot be reverted.\n";

        return false;
    }
}
