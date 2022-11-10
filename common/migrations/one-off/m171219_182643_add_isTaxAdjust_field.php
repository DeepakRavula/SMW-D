<?php

use yii\db\Migration;
use common\models\Invoice;

class m171219_182643_add_isTaxAdjust_field extends Migration
{
    public function up()
    {
        foreach (Invoice::find()->all() as $invoice) {
            $invoice->updateAttributes(['isTaxAdjusted' => false]);
        }
    }

    public function down()
    {
        echo "m171219_182643_add_isTaxAdjust_field cannot be reverted.\n";

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
