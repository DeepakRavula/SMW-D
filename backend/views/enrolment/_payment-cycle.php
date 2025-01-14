<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\components\gridView\KartikGridView;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use kartik\switchinput\SwitchInput;

?>

    <?php $columns = [
            'startDate:date',
            'endDate:date',
            [
                'label' => 'Status',
                'value' => function ($data) {
                    $status = null;
                    if ($data->isUnpaid()) {
                        $status = 'Owing';
                    } else if ($data->isFullyPaid()) {
                        $status = 'Paid';
                    } else {
                        $status = 'Partialy Paid';
                    }
    
                    return $status;
                }
            ],
            [
                'label' => 'Invoiced',
                'value' => function ($data) {
                    $invoiceStatus = null;
                    if ($data->hasInvoicedLesson()) {
                        $invoiceStatus = 'Invoiced';
                    } else {
                        $invoiceStatus = 'Not Invoiced';
                    }
                    return $invoiceStatus;
                }
            ],           
        ] ?>
<?php Pjax::begin(['id' => 'payment-cycle-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $paymentCycleDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'summary' => false,
        'emptyText' => false,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'options' => ['id' => 'enrolment-payment-cycle-grid'],
        'columns' => $columns,
    ]); ?>
<?php Pjax::end(); ?>
