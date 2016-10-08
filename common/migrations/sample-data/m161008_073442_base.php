<?php

use yii\db\Migration;

class m161008_073442_base extends Migration
{
    public function up()
    {
		$sql = file_get_contents(dirname(__FILE__).'/' . get_class($this) . '_' . __FUNCTION__ . '.sql');

		return $this->execute($sql);
    }

    public function down()
    {
        echo "m161008_073442_base cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
