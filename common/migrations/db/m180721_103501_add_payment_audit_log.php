<?php

use yii\db\Migration;

/**
 * Class m180721_103501_add_payment_audit_log
 */
class m180721_103501_add_payment_audit_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('payment', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('payment', 'createdByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('payment', 'updatedByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('proforma_invoice', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('proforma_invoice', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('proforma_invoice', 'createdByUserId', $this->integer()->unsigned()->notNull());
		$this->addColumn('proforma_invoice', 'updatedByUserId', $this->integer()->unsigned()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180721_103501_add_payment_audit_log cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180721_103501_add_payment_audit_log cannot be reverted.\n";

        return false;
    }
    */
}
