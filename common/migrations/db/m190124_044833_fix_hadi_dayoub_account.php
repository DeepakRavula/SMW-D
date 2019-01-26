<?php

use yii\db\Migration;
use common\models\Payment;
use common\models\User;
use common\models\Lesson;
use common\models\LessonHierarchy;
use common\models\LessonPayment;

/**
 * Class m190124_044833_fix_hadi_dayoub_account
 */
class m190124_044833_fix_hadi_dayoub_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function init() 
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    
    public function safeUp()
    {
        $lessons = Lesson::find()->andWhere(['>','id', 207450])->andWhere(['courseId' => 687])->all();
        foreach ($lessons as $lesson) {
            if(!$lesson->leafs) {
          if($lesson->hasRootLesson()) {  
          $immediateRootLesson = $lesson->immediateRootLesson; 
          if($immediateRootLesson->status === Lesson::STATUS_CANCELED){   
          $immediateRootLesson->status = Lesson::STATUS_SCHEDULED;
          $immediateRootLesson->save();
          }
          if($lesson->hasPayment()) {
             $lessonPayments = $lesson->lessonPayments;
             foreach($lessonPayments as $lessonPayment){
                $lessonPayment->updateAttributes(['lessonId' => $immediateRootLesson->id]);
                if ($lessonPayment->payment->creditUsage) {
                    $lessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['reference' => $immediateRootLesson->lessonNumber]);
                }
             }
          }
          if($lesson->invoice) {        
            $this->execute ("DELETE  iv, ip, ili, iil, iipcl, iie, n3, pfpf, t2, ilid, ir FROM invoice iv
            LEFT JOIN note n3 ON n3.`instanceId`= iv.`id` AND n3.`instanceType` = 4
            LEFT JOIN proforma_payment_frequency pfpf ON pfpf.`invoiceId`= iv.`id`
            LEFT JOIN invoice_line_item ili ON ili.`invoice_id`= iv.`id`
            LEFT JOIN invoice_item_lesson iil ON iil.`invoiceLineItemId`= ili.`id`
            LEFT JOIN invoice_item_enrolment iie ON iie.`invoiceLineItemId`= ili.`id`
            LEFT JOIN invoice_item_payment_cycle_lesson iipcl ON iipcl.`invoiceLineItemId`= ili.`id`
            LEFT JOIN invoice_line_item_discount ilid ON ilid.`invoiceLineItemId`= ili.`id`
            LEFT JOIN invoice_payment ip ON ip.`invoice_id`= iv.`id`
            LEFT JOIN invoice_reverse ir ON ir.`invoiceId`= iv.`id`
            LEFT JOIN transaction t2 ON t2.`id`= iv.`transactionId`
            where iv.id =".$lesson->invoice->id);
              }
         
          $this->execute("DELETE FROM bulk_reschedule_lesson where lessonId =".$lesson->id);
          $this->execute("DELETE FROM invoice_item_lesson where lessonId =".$lesson->id);
          $this->execute("DELETE FROM lesson_split_usage where lessonId =".$lesson->id);
          $this->execute("DELETE FROM  private_lesson where lessonId =".$lesson->id);
          $this->execute("DELETE FROM lesson where lesson.id = ".$lesson->id);
          $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = ".$immediateRootLesson->id." AND lesson_hierarchy.childLessonId =".$lesson->id);
          $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = ".$lesson->id." AND lesson_hierarchy.childLessonId =".$lesson->id);     

//          
          
        }     
    }
    }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190124_044833_fix_hadi_dayoub_account cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190124_044833_fix_hadi_dayoub_account cannot be reverted.\n";

        return false;
    }
    */
}
