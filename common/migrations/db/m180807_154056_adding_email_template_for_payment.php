<?php

use yii\db\Migration;
use common\models\EmailObject;
use common\models\EmailTemplate;

/**
 * Class m180807_154056_adding_email_template_for_payment
 */
class m180807_154056_adding_email_template_for_payment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $emailObject  =   new EmailObject();
        $emailObject->name    =  'Payment';
        $emailObject->save();
        $emailTemplate  =   new EmailTemplate();
        $emailTemplate->emailTypeId =   EmailObject::OBJECT_PAYMENT;
        $emailTemplate->subject     =   'Payment from Arcadia Academy of Music';
        $emailTemplate->header = 'Please find the Payment below';
        $emailTemplate->footer = 'Thank you Arcadia Academy of Music Team';
        $emailTemplate->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180807_154056_adding_email_template_for_payment cannot be reverted.\n";

        return false;
    }
}
