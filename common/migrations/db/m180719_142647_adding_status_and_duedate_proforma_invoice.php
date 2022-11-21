<?php

use yii\db\Migration;
use common\models\ProformaInvoice;

/**
 * Class m180719_142647_adding_status_and_duedate_proforma_invoice
 */
class m180719_142647_adding_status_and_duedate_proforma_invoice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('proforma_invoice', 'status', $this->integer()->notNull());  
        $this->addColumn('proforma_invoice', 'dueDate', $this->date()); 
        $proformaInvoices = ProformaInvoice::find()->all();
        foreach($proformaInvoices as $proformaInvoice) {
            $proformaInvoice->status = ProformaInvoice::STATUS_UNPAID;
            $proformaInvoice->dueDate = (new \DateTime())->format('Y-m-d');
            $proformaInvoice->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180719_142647_adding_status_and_duedate_proforma_invoice cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180719_142647_adding_status_and_duedate_proforma_invoice cannot be reverted.\n";

        return false;
    }
    */
}
