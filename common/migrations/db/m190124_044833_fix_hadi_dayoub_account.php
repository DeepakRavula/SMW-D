<?php

use common\models\Lesson;
use common\models\Payment;
use common\models\User;
use yii\db\Migration;
use yii\helpers\Console;
use common\models\LessonPayment;
use common\models\InvoicePayment;

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
        $deletedLessons = Lesson::find()->andWhere(['>','id' , '207450'])->andWhere(['courseId' => 687])->andWhere(['isDeleted' => true])->all();
         print_r(count($deletedLessons));
        foreach ($deletedLessons as $deletedLesson) {
            $deletedLesson->updateAttributes(['isDeleted' => false]);
            $deletedLessonPayments =  LessonPayment::find()->andWhere(['lessonId' => $deletedLesson->id])->andWhere(['isDeleted' => true])->all();
            foreach ($deletedLessonPayments as $deletedLessonPayment) {
                $deletedLessonPayment->updateAttributes(['isDeleted' => false]);
                $deletedLesson->payment->updateAttributes(['isDeleted' => false]);
                $deletedLessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['isDeleted' => false]);
                if ($deletedLessonPayment->payment->isCreditApplied()) {
                    $debitUsageInvoicePayments = InvoicePayment::find()->andWhere(['payment_id' => $deletedLessonPayment->payment->creditUsage->debitUsagePayment->id])->andWhere(['isDeleted' => true])->all();
                   foreach ($debitUsageInvoicePayments as $debitUsageInvoicePayment) {
                       $debitUsageInvoicePayment->updateAttributes(['isDeleted' => false]);
                   }
                }
            }
        }

        $lastChildLessonChildId = [211510];
        $lastChildLessonChilds = Lesson::find()->andWhere(['id' => $lastChildLessonChildId])->all();
        foreach ( $lastChildLessonChilds as  $lastChildLessonChild){
            if ( $lastChildLessonChild->rootLesson) {
                $immediateRootLesson = $lastChildLessonChild->parent()->one();
                if ($immediateRootLesson->status === Lesson::STATUS_CANCELED) {
                    $immediateRootLesson->updateAttributes(['status' => Lesson::STATUS_SCHEDULED]); 
                }
                if ($lastChildLessonChild->hasPayment()) {
                    $lastChildLessonChildPayments = $lastChildLessonChild->allLessonPayments;
                    foreach ($lastChildLessonChildPayments as $lastChildLessonChildPayment) {
                        $lastChildLessonChildPayment->updateAttributes(['lessonId' => $immediateRootLesson->id]);
                        $lastChildLessonChildPayment->updateAttributes(['isDeleted' => false]);
                        if ($lastChildLessonChildPayment->payment->creditUsage) {
                            $lastChildLessonChildPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['isDeleted' => false]);
                            $lastChildLessonChildPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['reference' => $immediateRootLesson->lessonNumber]);
                        }
                        if ($lastChildLessonChildPayment->payment->debitUsage) {
                            $lastChildLessonChildPayment->payment->debitUsage->creditUsagePayment->updateAttributes(['isDeleted' => false]);
                        }
                    }
                }
                if ($lastChildLessonChild->invoice) {
                    $this->execute("DELETE  iv, ip, ili, iil, iipcl, iie, n3, pfpf, t2, ilid, ir FROM invoice iv
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
        where iv.id =" .$lastChildLessonChild->invoice->id);
                }

                $this->execute("DELETE FROM bulk_reschedule_lesson where lessonId =" .$lastChildLessonChild->id);
                $this->execute("DELETE FROM invoice_item_lesson where lessonId =" .$lastChildLessonChild->id);
                $this->execute("DELETE FROM lesson_split_usage where lessonId =" .$lastChildLessonChild->id);
                $this->execute("DELETE FROM private_lesson where lessonId =" .$lastChildLessonChild->id);
                $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = " . $immediateRootLesson->id . " AND lesson_hierarchy.childLessonId =" .$lastChildLessonChild->id);
                $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = " .$lastChildLessonChild->id . " AND lesson_hierarchy.childLessonId =" .$lastChildLessonChild->id);
                $this->execute("DELETE FROM lesson where lesson.id = " .$lastChildLessonChild->id);

            }
          

}
        
        
        
        $lastChildLessonIds = [211247,219442];
        $childLessonIds = [207452];
        $lastChildLessons = Lesson::find()->andWhere(['id' => $lastChildLessonIds])->all();
        foreach ($lastChildLessons as $lastChildLesson){
            if ($lastChildLesson->rootLesson) {
                $immediateRootLesson =$lastChildLesson->parent()->one();
                if ($immediateRootLesson->status === Lesson::STATUS_CANCELED) {
                    $immediateRootLesson->updateAttributes(['status' => Lesson::STATUS_SCHEDULED]); 
                }
                if ($lastChildLesson->hasPayment()) {
                   $lastChildLessonPayments = $lastChildLesson->allLessonPayments;
                    foreach ($lastChildLessonPayments as $lastChildLessonPayment) {
                       $lastChildLessonPayment->updateAttributes(['lessonId' => $immediateRootLesson->id]);
                       $lastChildLessonPayment->updateAttributes(['isDeleted' => false]); 
                       if ($lastChildLessonPayment->payment->creditUsage) {
                           $lastChildLessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['isDeleted' => false]);
                           $lastChildLessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['reference' => $immediateRootLesson->lessonNumber]);
                        }
                        if ($lastChildLessonPayment->payment->debitUsage) {
                           $lastChildLessonPayment->payment->debitUsage->creditUsagePayment->updateAttributes(['isDeleted' => false]);
                        }
                    }
                }
                if ($lastChildLesson->invoice) {
                    $this->execute("DELETE  iv, ip, ili, iil, iipcl, iie, n3, pfpf, t2, ilid, ir FROM invoice iv
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
        where iv.id =" .$lastChildLesson->invoice->id);
                }

                $this->execute("DELETE FROM bulk_reschedule_lesson where lessonId =" .$lastChildLesson->id);
                $this->execute("DELETE FROM invoice_item_lesson where lessonId =" .$lastChildLesson->id);
                $this->execute("DELETE FROM lesson_split_usage where lessonId =" .$lastChildLesson->id);
                $this->execute("DELETE FROM private_lesson where lessonId =" .$lastChildLesson->id);
                $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = " . $immediateRootLesson->id . " AND lesson_hierarchy.childLessonId =" .$lastChildLesson->id);
                $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = " .$lastChildLesson->id . " AND lesson_hierarchy.childLessonId =" .$lastChildLesson->id);
                $this->execute("DELETE FROM lesson where lesson.id = " .$lastChildLesson->id);

            }
          

}

$childLessons = Lesson::find()->andWhere(['id' => $childLessonIds])->all();

        foreach ($childLessons as $childLesson){
            if ($childLesson->hasRootLesson()) {
                $immediateRootLesson =$childLesson->parent()->one();
                if ($immediateRootLesson->status === Lesson::STATUS_CANCELED) {
                    $immediateRootLesson->updateAttributes(['status' => Lesson::STATUS_SCHEDULED]); 
                }
                if ($childLesson->hasPayment()) {
                   $childLessonPayments = $childLesson->allLessonPayments;
                    foreach ($childLessonPayments as $childLessonPayment) {
                       $childLessonPayment->updateAttributes(['lessonId' => $immediateRootLesson->id]);
                       $childLessonPayment->updateAttributes(['isDeleted' => false]); 
                       if ($childLessonPayment->payment->creditUsage) {
                           $childLessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['isDeleted' => false]);
                           $childLessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['reference' => $immediateRootLesson->lessonNumber]);
                        }
                        if ($childLessonPayment->payment->debitUsage) {
                           $childLessonPayment->payment->debitUsage->creditUsagePayment->updateAttributes(['isDeleted' => false]);
                        }
                    }
                }
                if ($childLesson->invoice) {
                    $this->execute("DELETE  iv, ip, ili, iil, iipcl, iie, n3, pfpf, t2, ilid, ir FROM invoice iv
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
        where iv.id =" .$childLesson->invoice->id);
                }

                $this->execute("DELETE FROM bulk_reschedule_lesson where lessonId =" .$childLesson->id);
                $this->execute("DELETE FROM invoice_item_lesson where lessonId =" .$childLesson->id);
                $this->execute("DELETE FROM lesson_split_usage where lessonId =" .$childLesson->id);
                $this->execute("DELETE FROM private_lesson where lessonId =" .$childLesson->id);
                $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = " . $immediateRootLesson->id . " AND lesson_hierarchy.childLessonId =" .$childLesson->id);
                $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = " .$childLesson->id . " AND lesson_hierarchy.childLessonId =" .$childLesson->id);
                $this->execute("DELETE FROM lesson where lesson.id = " .$childLesson->id);

            }
          
    }
    $lessons = Lesson::find()->andWhere(['>', 'id', 207449])->andWhere(['courseId' => 687])->all();
        $count = count($lessons);
        Console::startProgress(0, $count, 'Deleting Lessons...');       
        foreach ($lessons as $lesson) {       
        if ($lesson->hasRootLesson()) {
                    $immediateRootLessonRoot = $lesson->parent()->one();
                    print_r("\nProcessing Lesson.".$lesson->id);
                    if ( $immediateRootLessonRoot->status === Lesson::STATUS_CANCELED) {
                        $immediateRootLessonRoot->updateAttributes(['status' => Lesson::STATUS_SCHEDULED]); 
                    }
                    if ($lesson->hasPayment()) {
                       
                        $lessonPayments = $lesson->allLessonPayments;
                        foreach ($lessonPayments as $lessonPayment) {
                            print_r("\n Lesson payment".$lessonPayment->id);
                            $lessonPayment->updateAttributes(['lessonId' => $immediateRootLessonRoot->id]);
                            $lessonPayment->updateAttributes(['isDeleted' => false]);
                            $lessonPayment->payment->updateAttributes(['isDeleted' => false]);
                            if ($lessonPayment->payment->creditUsage) {
                                $lessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['isDeleted' => false]);
                                $lessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['reference' => $immediateRootLessonRoot->lessonNumber]);
                            }
                            if ($lessonPayment->payment->debitUsage) {
                                $lessonPayment->payment->debitUsage->creditUsagePayment->updateAttributes(['isDeleted' => false]);
                            }
                        }
                    }
                    if ($lesson->invoice) {
                        $this->execute("DELETE  iv, ip, ili, iil, iipcl, iie, n3, pfpf, t2, ilid, ir FROM invoice iv
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
            where iv.id =" . $lesson->invoice->id);
                    }

                    $this->execute("DELETE FROM bulk_reschedule_lesson where lessonId =" . $lesson->id);
                    $this->execute("DELETE FROM invoice_item_lesson where lessonId =" . $lesson->id);
                    $this->execute("DELETE FROM lesson_split_usage where lessonId =" . $lesson->id);
                    $this->execute("DELETE FROM private_lesson where lessonId =" . $lesson->id);
                    $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = " . $immediateRootLessonRoot->id . " AND lesson_hierarchy.childLessonId =" . $lesson->id);
                    $this->execute("DELETE FROM lesson_hierarchy where lesson_hierarchy.lessonId = " . $lesson->id . " AND lesson_hierarchy.childLessonId =" . $lesson->id);
                    $this->execute("DELETE FROM lesson where lesson.id = " . $lesson->id);

                }
              
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);

        
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
