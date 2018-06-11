<?php

use yii\db\Migration;

/**
 * Class m180610_104202_saving_lineitem_in_proforma_lesson
 */
class m180610_104202_saving_lineitem_in_proforma_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
  /*  public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
  /*  public function safeDown()
    {
        echo "m180610_104202_saving_lineitem_in_proforma_lesson cannot be reverted.\n";

        return false;
    } */

    
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->truncateTable('proforma_invoice');
        $this->truncateTable('proforma_line_item');
        $this->truncateTable('proforma_item_lesson');
        $this->truncateTable('proforma_item_invoice');
        $this->addColumn('proforma_item_lesson', 'proformaLineItemId', $this->integer()->notNull()->after('lesson_id'));
        $this->addColumn('proforma_item_invoice', 'proformaLineItemId', $this->integer()->notNull()->after('invoice_id'));

    }

    public function down()
    {
        echo "m180610_104202_saving_lineitem_in_proforma_lesson cannot be reverted.\n";

        return false;
    }
    
}
