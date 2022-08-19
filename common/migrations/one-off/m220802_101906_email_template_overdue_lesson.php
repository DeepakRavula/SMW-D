<?php

use yii\db\Migration;
use common\models\EmailObject;
use common\models\EmailTemplate;

/**
 * Class m220802_101906_email_template_overdue_lesson
 */
class m220802_101906_email_template_overdue_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $emailobjects = EmailObject::find()
        ->andWhere(['id' => 9])->all();
  
        foreach ($emailobjects as $emailobject) {
        if($emailobject->name === EmailTemplate::OVERDUE_LESSON) {
            $emailtemplate = new EmailTemplate();
                $emailtemplate->emailTypeId =$emailobject->id;
                $emailtemplate->subject = 'Course from Arcadia Academy of Music';
                $emailtemplate->header = 'Please find the course below';
                $emailtemplate->footer = 'Thank you Arcadia Academy of Music Team';
                $emailtemplate->save();
        }
    }
}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220802_101906_email_template_overdue_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220802_101906_email_template_overdue_lesson cannot be reverted.\n";

        return false;
    }
    */
}
