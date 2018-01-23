<?php

use yii\db\Migration;

class m171219_182354_add_isTaxAdjust_field extends Migration
{
    public function up()
    {
        $this->addColumn('invoice', 'isTaxAdjusted', $this->integer()->notNull()->after('isDeleted'));
    }

    public function down()
    {
        echo "m171219_182354_add_isTaxAdjust_field cannot be reverted.\n";

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
