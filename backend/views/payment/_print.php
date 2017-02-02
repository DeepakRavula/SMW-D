<?php

use yii\grid\GridView;
use common\models\PaymentMethod;
use common\models\InvoicePayment;
use common\models\Invoice;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$total = 0;
if (!empty($paymentDataProvider->getModels())) {
    foreach ($paymentDataProvider->getModels() as $key => $val) {
        if ($groupByMethod) {
            $total    += $val->paymentMethod->getPaymentMethodTotal($fromDate, $toDate);
        } else {
            $total += $val->amount;
        }
    }
}
?>
<h3>Payments<center>Date : <?= $fromDate->format('d-m-Y') . ' to ' . $toDate->format('d-m-Y');?></center></h3>  

<?php
    if (! $groupByMethod) {
        $columns = [
            [
                'label' => 'ID',
                'value' => function ($data) {
                    return $data->invoicePayment->invoice->getInvoiceNumber();
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date);
                },
            ],
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
            ],
            [
                'label' => 'Customer',
                'value' => function ($data) {
                    return $data->user->publicIdentity;
                },
            ],
            [
                'label' => 'Reference Number',
                'value' => function ($data) {
                    if ((int) $data->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_APPLIED || (int) $data->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_USED) {
                        $invoiceNumber = str_pad($data->reference, 5, 0, STR_PAD_LEFT);
                        $invoicePayment = InvoicePayment::findOne(['payment_id' => $data->id]);
                        if ((int) $invoicePayment->invoice->type === Invoice::TYPE_INVOICE) {
                            return 'I - '.$invoiceNumber;
                        } else {
                            return 'P - '.$invoiceNumber;
                        }
                    } else {
                        return $data->reference;
                    }
                },
            ],
            [
                'label' => 'Amount',
                'value' => function ($data) {
                    return $data->amount;
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'enableSorting' => false,
                'footer' => Yii::$app->formatter->asCurrency($total),
            ],
        ];
    } else {
        $columns = [
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
            ],
            [
                'label' => 'Amount',
                'value' => function ($data) use ($fromDate, $toDate) {
                    return $data->paymentMethod->getPaymentMethodTotal($fromDate, $toDate);
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'enableSorting' => false,
                'footer' => Yii::$app->formatter->asCurrency($total),
            ],
        ];
    }
?>

    <?php echo GridView::widget([
        'dataProvider' => $paymentDataProvider,
        'showFooter' => true,
        'footerRowOptions' => ['style' => 'font-weight:bold;text-align: right;'],
        'columns' => $columns,
    ]); ?>


<script>
	$(document).ready(function(){
		window.print();
	});
</script>