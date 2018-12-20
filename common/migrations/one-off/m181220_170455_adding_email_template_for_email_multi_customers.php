<?php

use yii\db\Migration;
use common\models\EmailObject;
use common\models\EmailTemplate;

/**
 * Class m181220_170455_adding_email_template_for_email_multi_customers
 */
class m181220_170455_adding_email_template_for_email_multi_customers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $emailObject  =   new EmailObject();
        $emailObject->name    =  'Message';
        $emailObject->save();
       $emailTemplate  =   new EmailTemplate();
       $emailTemplate->emailTypeId =   EmailObject::OBJECT_MESSAGE;
       $emailTemplate->subject     =   'Message from Arcadia Academy of Music';
       $emailTemplate->header = 'Please find the message below';
       $emailTemplate->footer = 'Thank you Arcadia Academy of Music Team';
       $emailTemplate->save();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181220_170455_adding_email_template_for_email_multi_customers cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181220_170455_adding_email_template_for_email_multi_customers cannot be reverted.\n";

        return false;
    }
    */
}
