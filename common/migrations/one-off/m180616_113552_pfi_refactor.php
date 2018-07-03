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
        $proformaInvoices = Invoice::find()
            ->notDeleted()
            ->proFormaInvoice()
            ->location([14, 15])
            ->andWhere(['NOT', ['invoice.user_id'=> 0]])
            ->appliedPayments()
            ->all();
        foreach ($proformaInvoices as $proformaInvoice) {
            if (!$proformaInvoice->hasCreditUsed()) {
                foreach ($proformaInvoice->invoicePayments as $payment) {
                    $payment->delete();
                }
            } else if (!$proformaInvoice->hasLessonCreditUsedPayment()) {
                $appliedAmount = $proformaInvoice->invoiceAppliedPaymentTotal;
                if ($appliedAmount > $proformaInvoice->creditUsedPaymentTotal) {
                    $amountToReduce = $appliedAmount - abs($proformaInvoice->creditUsedPaymentTotal);
                    foreach ($proformaInvoice->manualPayments as $payment) {
                        if ($payment->amount <= $amountToReduce) {
                            $amountToReduce -= $payment->amount;
                            $payment->delete();
                            $proformaInvoice->save();
                        } else {
                            $payment->updateAttributes(['amount' => $payment->amount - $amountToReduce]);
                            $amountToReduce -= $payment->amount;
                            $payment->invoice->save();
                        }
                    }
                    foreach ($proformaInvoice->creditAppliedPayments as $payment) {
                        if ($payment->amount <= $amountToReduce) {
                            $amountToReduce -= $payment->amount;
                            $payment->delete();
                            $proformaInvoice->save();
                        } else {
                            $payment->updateAttributes(['amount' => $payment->amount - $amountToReduce]);
                            if ($payment->payment->creditUsage->debitUsagePayment) {
                                $payment->payment->creditUsage->debitUsagePayment->updateAttributes(['amount' => - ($payment->amount - $amountToReduce)]);
                                if ($payment->payment->creditUsage->debitUsagePayment->invoicePayment) {
                                    $payment->payment->creditUsage->debitUsagePayment->invoicePayment->updateAttributes(['amount' => - ($payment->amount - $amountToReduce)]);
                                } else if ($payment->payment->creditUsage->debitUsagePayment->lessonPayment) {
                                    $payment->payment->creditUsage->debitUsagePayment->lessonPayment->updateAttributes(['amount' => - ($payment->amount - $amountToReduce)]);
                                }
                            }
                            $amountToReduce -= $payment->amount;
                            $payment->invoice->save();
                        }
                    }
                }
            }
        }

        $invoices = Invoice::find()
            ->notDeleted()
            ->invoice()
            ->location([14, 15])
            ->andWhere(['<', 'balance', 0])
            ->andWhere(['NOT', ['invoice.user_id'=> 0]])
            ->manualPayments()
            ->all();
        foreach ($invoices as $invoice) {
            $amount = $invoice->invoiceAppliedPaymentTotal;
            if ($amount > $invoice->total) {
                foreach ($invoice->manualPayments as $payment) {
                    $balance = abs($invoice->balance);
                    if ($payment->amount <= $balance) {
                        $balance -= $payment->amount;
                        $payment->delete();
                        $invoice->save();
                    } else {
                        $payment->updateAttributes(['amount' => $payment->amount - $balance]);
                        $payment->invoice->save();
                    }
                }
            }
            $invoice->save();
        }

        $locationId = [14, 15];
        $lineItems = InvoiceLineItem::find()
            ->notDeleted()
            ->joinWith(['invoice' => function ($query) use ($locationId) {
                $query->andWhere(['invoice.location_id' => $locationId])
                ->notDeleted();
            }])
            ->lessonItem()
            ->all();
        foreach ($lineItems as $lineItem) {
            if($lineItem->lesson) {
                if (!$lineItem->isGroupLesson()) {
                    if ($lineItem->hasLineItemDiscount()) {
                        $discount = new LessonDiscount();
                        $discount->lessonId = $lineItem->lesson->id;
                        if ($lineItem->lesson->hasLineItemDiscount()) {
                            $discount = $lineItem->lesson->lineItemDiscount;
                        }
                        $discount->value = $lineItem->lineItemDiscount->value;
                        $discount->valueType = $lineItem->lineItemDiscount->valueType;
                        $discount->type = $lineItem->lineItemDiscount->type;
                        $discount->save();
                    }
                    if ($lineItem->hasMultiEnrolmentDiscount()) {
                        $discount = new LessonDiscount();
                        $discount->lessonId = $lineItem->lesson->id;
                        if ($lineItem->lesson->hasMultiEnrolmentDiscount()) {
                            $discount = $lineItem->lesson->multiEnrolmentDiscount;
                        }
                        $discount->value = $lineItem->multiEnrolmentDiscount->value;
                        $discount->valueType = $lineItem->multiEnrolmentDiscount->valueType;
                        $discount->type = $lineItem->multiEnrolmentDiscount->type;
                        $discount->save();
                    }
                    if ($lineItem->hasCustomerDiscount()) {
                        $discount = new LessonDiscount();
                        $discount->lessonId = $lineItem->lesson->id;
                        if ($lineItem->lesson->hasCustomerDiscount()) {
                            $discount = $lineItem->lesson->customerDiscount;
                        }
                        $discount->value = $lineItem->customerDiscount->value;
                        $discount->valueType = $lineItem->customerDiscount->valueType;
                        $discount->type = $lineItem->customerDiscount->type;
                        $discount->save();
                    }
                    if ($lineItem->hasEnrolmentPaymentFrequencyDiscount()) {
                        $discount = new LessonDiscount();
                        $discount->lessonId = $lineItem->lesson->id;
                        if ($lineItem->lesson->hasEnrolmentPaymentFrequencyDiscount()) {
                            $discount = $lineItem->lesson->enrolmentPaymentFrequencyDiscount;
                        }
                        $discount->value = $lineItem->enrolmentPaymentFrequencyDiscount->value;
                        $discount->valueType = $lineItem->enrolmentPaymentFrequencyDiscount->valueType;
                        $discount->type = $lineItem->enrolmentPaymentFrequencyDiscount->type;
                        $discount->save();
                    }
                }
            }
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
