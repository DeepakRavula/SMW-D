<?php

use yii\db\Migration;

class m160822_175733_credit_usage_schema extends Migration
{
    public function up()
    {
		$sql = file_get_contents(dirname(__FILE__).'/' . get_class($this) . '_' . __FUNCTION__ . '.sql');
        
		return $this->execute($sql);
    }

    public function down()
    {
        echo "m160822_175733_credit_usage_schema cannot be reverted.\n";

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
