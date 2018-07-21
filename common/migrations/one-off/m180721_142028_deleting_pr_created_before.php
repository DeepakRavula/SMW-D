<?php

use yii\db\Migration;
use common\models\ProformaInvoice;
use common\models\User;

/**
 * Class m180721_142028_deleting_pr_created_before
 */
class m180721_142028_deleting_pr_created_before extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $proformaInvoices = ProformaInvoice::find()
                ->andWhere([ '<', 'id', 172])
                ->all();         
        foreach ($proformaInvoices as $proformaInvoice) {
        $proformaInvoice->updateAttributes(['isDeleted' => true]);
        } 

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180721_142028_deleting_pr_created_before cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180721_142028_deleting_pr_created_before cannot be reverted.\n";

        return false;
    }
    */
}
