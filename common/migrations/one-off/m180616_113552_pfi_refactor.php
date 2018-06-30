<?php
use yii\db\Migration;
use common\models\Invoice;
use common\models\Payment;
use common\models\User;
use common\models\Transaction;
use common\models\OpeningBalance;
use common\models\CustomerPayment;
use common\models\PaymentMethod;
use common\models\InvoicePayment;
use common\models\LessonPayment;
use common\models\InvoiceLineItem;
use common\models\discount\LessonDiscount;
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
        
        $invoices = Invoice::find()
            ->notDeleted()
            ->location([14, 15])
            ->andWhere(['<', 'balance', 0])
            ->andWhere(['NOT', ['invoice.user_id'=> 0]])
            ->manualPayments()
            ->all();
        foreach ($invoices as $invoice) {
            $amount = 0;
            if ($invoice->hasManualPayments()) {
                foreach ($invoice->manualPayments as $payment) {
                    $amount += $payment->amount;
                }
            }
            if ($amount > $invoice->total) {
                foreach ($invoice->manualPayments as $payment) {
                    $balance = abs($invoice->balance);
                    if ($payment->amount < $balance) {
                        $balance = $balance - $payment->amount;
                        $payment->delete();
                    } else {
                        $payment->updateAttributes(['amount' => $payment->amount - $balance]);
                        $payment->invoice->save();
                    }
                }
            }
            $invoice->save();
        }

        $proformaInvoices = Invoice::find()
            ->notDeleted()
            ->proFormaInvoice()
            ->location([14, 15])
            ->andWhere(['NOT', ['invoice.user_id'=> 0]])
            ->manualPayments()
            ->all();
        foreach ($proformaInvoices as $proformaInvoice) {
            if (!$proformaInvoice->hasCreditUsed()) {
                foreach ($proformaInvoice->manualPayments as $payment) {
                    $payment->delete();
                }
            }
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
            if($lineItem->lesson)
            {
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
