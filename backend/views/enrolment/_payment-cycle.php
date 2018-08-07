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
            'endDate:date'
        ]
    ]); ?>
<?php Pjax::end(); ?>
    

