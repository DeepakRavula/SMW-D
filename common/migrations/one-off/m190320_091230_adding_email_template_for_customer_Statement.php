<?php

use yii\db\Migration;
use common\models\EmailObject;
use common\models\EmailTemplate;
/**
 * Class m190320_091230_adding_email_template_for_customer_Statement
 */
class m190320_091230_adding_email_template_for_customer_Statement extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $emailObject  =   new EmailObject();
        $emailObject->name    =  'CustomerStatement';
        $emailObject->save();
       $emailTemplate  =   new EmailTemplate();
       $emailTemplate->emailTypeId =   EmailObject::OBJECT_CUSTOMER_STATEMENT;
       $emailTemplate->subject     =   'Customer Statement from Arcadia Academy of Music';
       $emailTemplate->header = 'Please find the customer statement below';
       $emailTemplate->footer = 'Thank you Arcadia Academy of Music Team';
       $emailTemplate->save();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190320_091230_adding_email_template_for_customer_Statement cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190320_091230_adding_email_template_for_customer_Statement cannot be reverted.\n";

        return false;
    }
    */
}
