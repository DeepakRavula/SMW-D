<?php

use yii\db\Migration;

/**
 * Class m180720_222523_adding_is_mail_sent_status_proforma_invoice
 */
class m180720_222523_adding_is_mail_sent_status_proforma_invoice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            'proforma_invoice',
            'isMailSent',
            $this->boolean()->notNull()->after('dueDate')
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180720_222523_adding_is_mail_sent_status_proforma_invoice cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180720_222523_adding_is_mail_sent_status_proforma_invoice cannot be reverted.\n";

        return false;
    }
    */
}
