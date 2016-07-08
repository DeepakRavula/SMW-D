<?php

use yii\db\Migration;

class m160708_094507_smw_base extends Migration
{
	public function up()
	{
        $sql = file_get_contents(dirname(__FILE__).'/' . get_class($this) . '_' . __FUNCTION__ . '.sql');
        
		return $this->execute($sql);
	}

	public function down()
	{
		echo "m160708_094507_smw_base does not support migration down.\n";
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
