<?php

use yii\db\Migration;
use common\models\EmailObject;
use common\models\EmailTemplate;
class m180202_085115_add_email_templates extends Migration
{
    public function up()
    {
          $emailobjects = EmailObject::find()
                ->all();
          
        foreach ($emailobjects as $emailobject) {
           if($emailobject->name === EmailTemplate::COURSE) {
               $emailtemplate = new EmailTemplate();
                $emailtemplate->emailTypeId =$emailobject->id;
                $emailtemplate->subject = 'Course from Arcadia Academy of Music';
                $emailtemplate->header = 'Please find the course below';
                $emailtemplate->footer = 'Thank you Arcadia Academy of Music Team';
                $emailtemplate->save();
          }
          if($emailobject->name === EmailTemplate::INVOICE) {
                $emailtemplate = new EmailTemplate();
                $emailtemplate->emailTypeId =$emailobject->id;
                $emailtemplate->subject = 'Invoice from Arcadia Academy of Music';
                $emailtemplate->header = 'Please find the invoice below';
                $emailtemplate->footer = 'Thank you Arcadia Academy of Music Team';
                $emailtemplate->save();
          }
          if($emailobject->name === EmailTemplate::LESSON) {
                $emailtemplate = new EmailTemplate();
                $emailtemplate->emailTypeId =$emailobject->id;
                $emailtemplate->subject = 'Lesson from Arcadia Academy of Music';
                $emailtemplate->header = 'Please find the lesson below';
                $emailtemplate->footer = 'Thank you Arcadia Academy of Music Team';
                $emailtemplate->save();
          }
          if($emailobject->name === EmailTemplate::PROFORMA_INVOICE) {
                $emailtemplate = new EmailTemplate();
                $emailtemplate->emailTypeId =$emailobject->id;
                $emailtemplate->subject = 'ProformaInvoice from Arcadia Academy of Music';
                $emailtemplate->header = 'Please find the ProformaInvoice below';
                $emailtemplate->footer = 'Thank you Arcadia Academy of Music Team';
                $emailtemplate->save();
          }
        }
    }

    public function down()
    {
        echo "m180202_085115_add_email_templates cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
