<?php

use yii\db\Migration;
use common\models\EmailObject;
use common\models\EmailTemplate;
/**
 * Class m180715_080319_adding_email_template_for_receipt
 */
class m180715_080319_adding_email_template_for_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $emailObject  =   new EmailObject();
         $emailObject->name    =  'Receipt';
         $emailObject->save();
        $emailTemplate  =   new EmailTemplate();
        $emailTemplate->emailTypeId =   EmailObject::OBJECT_RECEIPT;
        $emailTemplate->subject     =   'Receipt from Arcadia Academy of Music';
        $emailTemplate->header = 'Please find the receipt below';
        $emailTemplate->footer = 'Thank you Arcadia Academy of Music Team';
        $emailTemplate->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180715_080319_adding_email_template_for_receipt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180715_080319_adding_email_template_for_receipt cannot be reverted.\n";

        return false;
    }
    */
}
