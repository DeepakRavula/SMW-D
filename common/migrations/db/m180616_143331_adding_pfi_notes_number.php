<?php

use yii\db\Migration;

/**
 * Class m180616_143331_adding_pfi_notes_number
 */
class m180616_143331_adding_pfi_notes_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('proforma_invoice', 'proforma_invoice_number', $this->integer()->notNull());
        $this->addColumn('proforma_invoice', 'notes', $this->text());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180616_143331_adding_pfi_notes_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180616_143331_adding_pfi_notes_number cannot be reverted.\n";

        return false;
    }
    */
}
