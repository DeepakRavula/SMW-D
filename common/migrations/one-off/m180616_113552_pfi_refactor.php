<?php
use yii\db\Migration;
use common\models\Invoice;
use common\models\User;
use common\models\InvoicePayment;
use common\models\LessonPayment;
use common\models\InvoiceLineItem;
use common\models\discount\LessonDiscount;
use common\models\Lesson;
/**
 * Class m180616_113552_pfi_refactor
 */
class m180616_113552_pfi_refactor extends Migration
{
    public function init() 
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $invoicePayments = InvoicePayment::find()->all();
        $lessonPayments = LessonPayment::find()->all();

        foreach ($invoicePayments as $invoicePayment) {
            if ($invoicePayment->payment) {
                $invoicePayment->updateAttributes(['amount' => $invoicePayment->payment->amount]);
            }
        }

        foreach ($lessonPayments as $lessonPayment) {
            if ($lessonPayment->payment) {
                $lessonPayment->updateAttributes(['amount' => $lessonPayment->payment->amount]);
            }
        }

        $proformaInvoices = Invoice::find()
            ->notDeleted()
            ->proFormaInvoice()
            ->location([14, 15])
            ->andWhere(['NOT', ['invoice.user_id'=> 0]])
            ->lessonCreditUsed()
            ->all();
        foreach ($proformaInvoices as $proformaInvoice) {
            foreach ($proformaInvoice->lessonCreditUsedPayment as $invoicePayment) {
                if ($invoicePayment->payment->debitUsage->creditUsagePayment->lessonPayment->lesson) {
                    $lesson = $invoicePayment->payment->debitUsage->creditUsagePayment->lessonPayment->lesson;
                    if ($lesson->isPrivate()) {
                        $leafs = $lesson->leafs;
                        if ($leafs) {
                            foreach ($leafs as $leaf) {
                                $parent = $leaf->parent()->one();
                                foreach ($parent->getCreditUsedPayment($parent->enrolment->id) as $lessonPayment) {
                                    $iPayment = new InvoicePayment();
                                    $iPayment->invoice_id = $proformaInvoice->id;
                                    $iPayment->payment_id = $lessonPayment->payment->id;
                                    $iPayment->amount = $lessonPayment->amount;
                                    $iPayment->save();
                                }
                                $lessonPayment->updateAttributes([
                                    'isDeleted' => true
                                ]);
                                foreach ($leaf->getCreditAppliedPayment($leaf->enrolment->id) as $lessonPayment) {
                                    $lessonPayment->payment->updateAttributes([
                                        'reference' => $lessonPayment->payment->creditUsage->debitUsagePayment->invoice->invoiceNumber
                                    ]);
                                }
                            }
                        }
                        if ($leafs) {
                            $invoicePayment->updateAttributes(['isDeleted' => true]);
                        }
                    }
                }
            }
        }

        $cancelledLessons = Lesson::find()
            ->canceled()
            ->location([14, 15])
            ->all();

        foreach ($cancelledLessons as $cancelledLesson) {
            foreach ($cancelledLesson->lessonPayments as $lessonPayment) {
                $lessonPayment->updateAttributes(['isDeleted' => true]);
                $lessonPayment->payment->updateAttributes(['isDeleted' => true]);
            }
        }

        $invoices = Invoice::find()
            ->notDeleted()
            ->location([14, 15])
            ->all();

        foreach ($invoices as $invoice) {
            $invoice->save();
        }
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180616_113552_pfi_refactor cannot be reverted.\n";
        return false;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
    }
    public function down()
    {
        echo "m180616_113552_pfi_refactor cannot be reverted.\n";
        return false;
    }
    */
}
