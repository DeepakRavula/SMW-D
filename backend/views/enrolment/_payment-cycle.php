<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

?>
<?php Pjax::begin(['id' => 'payment-cycle-listing']); ?>
    <?= GridView::widget([
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
                'label' => 'Status',
                'value' => function ($data) {
                    $status = null;
                    if ($data->hasUnpaidLesson()) {
                        $status = 'Owing';
                    }
                    if ($data->hasPartialyPaidLesson()) {
                        $status = 'Partialy Paid';
                    }
                    if ($data->isFullyPaid()) {
                        $status = 'Paid';
                    }
                    return $status;
                }
            ]
        ]
    ]); ?>
<?php Pjax::end(); ?>
    

