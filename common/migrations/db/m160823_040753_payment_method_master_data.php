<?php

use yii\db\Migration;

class m160823_040753_payment_method_master_data extends Migration
{
    public function up()
    {
		$sql = file_get_contents(dirname(__FILE__).'/' . get_class($this) . '_' . __FUNCTION__ . '.sql');
        
		return $this->execute($sql);

    }

    public function down()
    {
        echo "m160823_040753_payment_method_master_data cannot be reverted.\n";

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
