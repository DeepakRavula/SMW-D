<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

?>
<?php yii\widgets\Pjax::begin(['id' => 'payment-cycle-listing']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $paymentCycleDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'summary' => false,
        'emptyText' => false,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'options' => ['id' => 'enrolment-payment-cycle-grid'],
        'columns' => [
            'startDate:date',
            'endDate:date',
            [
                'label' => 'Due Date',
                'value' => function ($data) {
                    return !empty($data->proFormaInvoice->dueDate) ?
                        Yii::$app->formatter->asDate($data->proFormaInvoice->dueDate) : '-';
                }
            ],
            [
                'label' => 'Pro-Forma Invoice',
                'value' => function ($data) {
                    $invoiceNumber = '-';
                    if ($data->hasProformaInvoice()) {
                        $invoiceNumber = $data->proFormaInvoice->getInvoiceNumber();
                    }
                    return $invoiceNumber;
                }
            ],
            [
                'label' => 'Status',
                'value' => function ($data) {
                    $result = 'Owing';
                    if (empty($data->proFormaInvoice)) {
                        $result = '-';
                    }
                    if (!empty($data->proFormaInvoice) && $data->proFormaInvoice->isPaid()) {
                        $result = 'Paid';
                    }
                    return $result;
                }

            ],
        ],
    ]); ?>
<?php yii\widgets\Pjax::end(); ?>
    

