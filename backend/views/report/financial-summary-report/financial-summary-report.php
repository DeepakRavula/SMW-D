<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use Yii;
use common\models\User;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\jui\DatePicker;

?>
<style>
<style>
  .table > tbody > tr.success > td ,.table > tbody > tr.kv-grid-group-row > td{
	background-color: white !important;
}
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: white !important;
}
.table > thead:first-child > tr:first-child > th{
    color : black;
    background-color : lightgray;
}
</style>
<div class="clearfix"></div>
<?php Pjax::begin(['id' => 'prepaid-future-group-locations-listing']); ?>
   
    <?= KartikGridView::widget([
        'id' => 'prepaid-future-group-id',
        'dataProvider' => $paidFutureGroupLessonsdataProvider,
        'rowOptions' =>  ['class' => 'financial-summary-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            '{export}',
            '{toggleData}',
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Prepaid Future Group Lessons',
        ],
        
        'showFooter' =>true,
        'columns' => [
            [
                'label' => 'Lesson ID',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->lessonId;
                },
            ],
            [
                'label' => 'Student Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->enrolment->student->fullName;
                },
            ],
            [
                'label' => 'Customer Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->enrolment->student->customer->publicIdentity;
                },
            ],
            [
                'label' => 'Date',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asDate($data->lesson->date) . ' @ ' . Yii::$app->formatter->asTime($data->lesson->date);
                },
                
            ],
            [
                'label' => 'Duration',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  (new \DateTime($data->lesson->duration))->format('H:i');
                },
            ],
            [
                'label' => 'Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data)   {
                    $amount = Yii::$app->formatter->asCurrency(round($data->total, 2));
                    return  $amount;
                },
            ],
            [
                'label' => 'Paid Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $paid = $data->total - $data->balance;
                    return  Yii::$app->formatter->asCurrency(round($paid, 2));
                },
            ],
            [
                'label' => 'Balance',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asCurrency($data->balance);
                },
            ],
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'future-group-amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>
<?php Pjax::begin(['id' => 'prepaid-past-unscheduled-group-locations-listing']); ?>
   
    <?= KartikGridView::widget([
        'id' => 'prepaid-past-unscheduled-group-id',
        'dataProvider' => $paidPastGroupLessonsdataProvider,
        'rowOptions' =>  ['class' => 'financial-summary-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            '{export}',
            '{toggleData}',
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Paid Unscheduled Group Lessons',
        ],
        
        'showFooter' =>true,
        'columns' => [
            [
                'label' => 'Lesson ID',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->lessonId;
                },
            ],
            [
                'label' => 'Student Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->enrolment->student->fullName;
                },
            ],
            [
                'label' => 'Customer Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->enrolment->student->customer->publicIdentity;
                },
            ],
            [
                'label' => 'Date',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asDate($data->lesson->date) . ' @ ' . Yii::$app->formatter->asTime($data->lesson->date);
                },
                
            ],
            [
                'label' => 'Duration',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  (new \DateTime($data->lesson->duration))->format('H:i');
                },
            ],
            [
                'label' => 'Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data)   {
                    $amount = Yii::$app->formatter->asCurrency(round($data->total, 2));
                    return  $amount;
                },
            ],
            [
                'label' => 'Paid Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $paid = $data->total - $data->balance;
                    return  Yii::$app->formatter->asCurrency(round($paid, 2));
                },
            ],
            [
                'label' => 'Balance',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asCurrency($data->balance);
                },
            ],
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'past-unscheduled-group-amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>
<?php Pjax::begin(['id' => 'prepaid-future-locations-listing']); ?>
   
    <?= KartikGridView::widget([
        'id' => 'prepaid-future-id',
        'dataProvider' => $paidFutureLessondataProvider,
        'rowOptions' =>  ['class' => 'financial-summary-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'filterModel' => $paidFutureLessonsSearchModel,
        'toolbar' =>  [
            '{export}',
            '{toggleData}',
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Prepaid Future Lessons',
        ],
        
        'showFooter' =>true,
        'columns' => [
            [
                'label' => 'Lesson ID',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->id;
                },
            ],
            [
                'label' => 'Student Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->enrolment->student->fullName;
                },
            ],
            [
                'label' => 'Customer Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->enrolment->student->customer->publicIdentity;
                },
            ],
            [
                'label' => 'Date',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'filter' => DatePicker::widget([
                    'model'=>$paidFutureLessonsSearchModel,
                    'attribute'=>'goToDate',
                    'dateFormat' => 'yyyy-MM-dd',
                ]),
                'format' => 'html',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asDate($data->date) . ' @ ' . Yii::$app->formatter->asTime($data->date);
                },
                
            ],
            [
                'label' => 'Duration',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  (new \DateTime($data->duration))->format('H:i');
                },
            ],
            [
                'label' => 'Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $amount = Yii::$app->formatter->asCurrency(round($data->privateLesson->total ?? 0, 2));
                    return  $amount;
                },
            ],
            [
                'label' => 'Paid Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $lessonPaid = !empty($data->getCreditAppliedAmount($data->enrolment->id)) ? $data->getCreditAppliedAmount($data->enrolment->id) : 0;
                    return  Yii::$app->formatter->asCurrency(round($lessonPaid, 2));
                },
            ],
            [
                'label' => 'Balance',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $balance = (round($data->privateLesson->balance ?? 0, 2) > 0.00 && round($data->privateLesson->balance ?? 0, 2) <= 0.09) || (round($data->privateLesson->balance ?? 0, 2) < 0.00 && round($data->privateLesson->balance ?? 0, 2) >= -0.09)  ? round('0.00', 2) : (round($data->privateLesson->balance ?? 0, 2));
                    return  Yii::$app->formatter->asCurrency($balance);
                },
            ],
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'future-amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>

<?php Pjax::begin(['id' => 'paid-unschedule-locations-listing']); ?>
    <?= KartikGridView::widget([
        'id' => 'paid-unschedule-id',
        'dataProvider' => $paidPastLessondataProvider,
        'rowOptions' =>  ['class' => 'financial-summary-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            '{export}',
            '{toggleData}',
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Paid Unscheduled Lessons',
        ],
        'showFooter' =>true,
        'columns' => [
            [
                'label' => 'Lesson ID',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->id;
                },
            ],
            [
                'label' => 'Student Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->enrolment->student->fullName;
                },
            ],
            [
                'label' => 'Customer Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->enrolment->student->customer->publicIdentity;
                },
            ],
            [
                'label' => 'Date',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asDate($data->date) . ' @ ' . Yii::$app->formatter->asTime($data->date);
                },
            ],
            [
                'label' => 'Duration',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  (new \DateTime($data->duration))->format('H:i');
                },
            ],
            [
                'label' => 'Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $amount = Yii::$app->formatter->asCurrency(round($data->privateLesson->total ?? 0, 2));
                    return  $amount;
                },
            ],
            [
                'label' => 'Paid Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $lessonPaid = !empty($data->getCreditAppliedAmount($data->enrolment->id)) ? $data->getCreditAppliedAmount($data->enrolment->id) : 0;
                    return  Yii::$app->formatter->asCurrency(round($lessonPaid, 2));
                },
            ],
            [
                'label' => 'Balance',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $balance = (round($data->privateLesson->balance ?? 0, 2) > 0.00 && round($data->privateLesson->balance ?? 0, 2) <= 0.09) || (round($data->privateLesson->balance ?? 0, 2) < 0.00 && round($data->privateLesson->balance ?? 0, 2) >= -0.09)  ? round('0.00', 2) : (round($data->privateLesson->balance ?? 0, 2));
                    return  Yii::$app->formatter->asCurrency($balance);
                },
            ],
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'past-amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>

<?php Pjax::begin(['id' => 'active-outstanding-locations-listing']); ?>
    <?= KartikGridView::widget([
        'id' => '1',
        'dataProvider' => $activeInvoicedataProvider,
        'rowOptions' =>  ['class' => 'financial-summary-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            '{export}',
            '{toggleData}',
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Active Outstanding Invoices',
        ],
        'showFooter' =>true,
        'columns' => [
            [
                'label' => 'Invoice ID',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->id;
                },
            ],
            [
                'label' => 'Customer Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $customer = User::findOne(['id' => $data->user_id]);
                    if ($customer) {
                        return $customer->publicIdentity;
                    } else {
                        return "NULL";
                    }
                },
            ],
            [
                'label' => 'Date',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  (new \DateTime($data->date))->format('M d, Y');
                },
            ],
            [
                'label' => 'Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asCurrency(round($data->total, 2));
                },
            ],
            [
                'label' => 'Paid Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $paymentTotal = !empty($data->invoicePaymentTotal) ? $data->invoicePaymentTotal : 0;
                    return  Yii::$app->formatter->asCurrency(round($paymentTotal, 2));
                },
            ],
            [
                'label' => 'Balance',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  (round($data->balance, 2) > 0.00 && round($data->balance, 2) <= 0.09) || (round($data->balance, 2) < 0.00 && round($data->balance, 2) >= -0.09)  ? Yii::$app->formatter->format(round('0.00', 2), ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                ]]): Yii::$app->formatter->format(round($data->balance, 2), ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                ]]);
                },
            ],
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'active-invoice-amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>

<?php Pjax::begin(['id' => 'inactive-outstanding-locations-listing']); ?>
    <?= KartikGridView::widget([
        'id' => 'inactive-outstanding-id',
        'dataProvider' => $inactiveInvoicedataProvider,
        'rowOptions' =>  ['class' => 'financial-summary-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            '{export}',
            '{toggleData}',
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Inactive Outstanding Invoices',
        ],
        'showFooter' =>true,
        'columns' => [
            [
                'label' => 'Invoice ID',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->id;
                },
            ],
            [
                'label' => 'Customer Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $customer = User::findOne(['id' => $data->user_id]);
                    if ($customer) {
                        return $customer->publicIdentity;
                    } else {
                        return "NULL";
                    }
                },
            ],
            [
                'label' => 'Date',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  (new \DateTime($data->date))->format('M d, Y');
                },
            ],
            [
                'label' => 'Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asCurrency(round($data->total, 2));
                },
            ],
            [
                'label' => 'Paid Amount',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $paymentTotal = !empty($data->invoicePaymentTotal) ? $data->invoicePaymentTotal : 0;
                    return  Yii::$app->formatter->asCurrency(round($paymentTotal, 2));
                },
            ],
            [
                'label' => 'Balance',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  (round($data->balance, 2) > 0.00 && round($data->balance, 2) <= 0.09) || (round($data->balance, 2) < 0.00 && round($data->balance, 2) >= -0.09)  ? Yii::$app->formatter->format(round('0.00', 2), ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                ]]): Yii::$app->formatter->format(round($data->balance, 2), ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                ]]);
                },
            ],
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'inactive-invoice-amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>

<?php Pjax::begin(['id' => 'active-customer-credit-locations-listing']); ?>
    <?= KartikGridView::widget([
        'id' => 'active-customer-id',
        'dataProvider' => $activeCustomerWithCreditdataProvider,
        'rowOptions' =>  ['class' => 'financial-summary-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            '{export}',
            '{toggleData}',
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Active Customers With Credit',
        ],
        'showFooter' =>true,
        'columns' => [
            [
                'label' => 'Customer ID',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->customerId;
                },
            ],
            [
                'label' => 'Customer Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $customer = User::findOne(['id' => $data->customerId]);
                    return  $customer->publicIdentity;
                },
            ],
            [
                'label' => 'Balance',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asCurrency(round($data->balance, 2));
                },
            ],
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'active-credit-amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>

<?php Pjax::begin(['id' => 'inactive-customer-creditlocations-listing']); ?>
    <?= KartikGridView::widget([
        'id' => 'inactive-customer-id',
        'dataProvider' => $inactiveCustomerWithCreditdataProvider,
        'rowOptions' =>  ['class' => 'financial-summary-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            '{export}',
            '{toggleData}',
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Inactive Customers With Credit',
        ],
        'showFooter' =>true,
        'columns' => [
            [
                'label' => 'Customer ID',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  $data->customerId;
                },
            ],
            [
                'label' => 'Customer Name',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    $customer = User::findOne(['id' => $data->customerId]);
                    return  $customer->publicIdentity;
                },
            ],
            [
                'label' => 'Balance',
                'headerOptions' => ['class' => 'warning', 'style' => 'background-color: lightgray'],
                'format' => 'html',
                'value' => function ($data) {
                    return  Yii::$app->formatter->asCurrency(round($data->balance, 2));
                },
            ],
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'inactive-credit-amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>

<?php Pjax::begin([
    'id' => 'amount-summary']) ?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Summary',
    'withBorder' => true,
])
?>
<table style="width:100%">
  <tr>
    <th style="width:80%"><u>Particulars</u></th>
    <th style="width:10%"><u>Count</u></th>
    <th style="width:10%"><u>Total</u></th>
  </tr>
  <tr>
    <th><hr></th>
    <th><hr></th>
    <th><hr></th>
  </tr>
  <tr>
    <td style="width:80%"><b>Prepaid Future Group Lessons</b></td>
    <td style="width:10%"><b><?= $paidFutureGroupLessonsCount ?></b></td>
    <td style="width:10%"><b><?=  Yii::$app->formatter->asCurrency(round(array_sum($paidFutureGroupLessonsSum), 2)) ?></b></td>
  </tr>
  <tr>
    <td style="width:80%"><b>Paid Unscheduled Group Lessons</b></td>
    <td style="width:10%"><b><?= $paidPastGroupLessonsCount ?></b></td>
    <td style="width:10%"><b><?=  Yii::$app->formatter->asCurrency(round(array_sum($paidPastGroupLessonsSum), 2)) ?></b></td>
  </tr>
  <tr>
  <tr>
    <td style="width:80%"><b>Prepaid Future Lessons</b></td>
    <td style="width:10%"><b><?= $paidFutureLessondataProvider->query->count() ?></b></td>
    <td style="width:10%"><b><?= Yii::$app->formatter->asCurrency($paidFutureLessondataProvider->query->sum('lesson_payment.amount')) ?></b></td>
  </tr>
  <tr>
    <td style="width:80%"><b>Paid Unscheduled Lessons</b></td>
    <td style="width:10%"><b><?= $paidPastLessonsCount; ?></b></td>
    <td style="width:10%"><b><?= Yii::$app->formatter->asCurrency($paidPastLessonsSum); ?></b></td>
  </tr>
  <tr>
    <td style="width:80%"><b>Active Outstanding Invoices</b></td>
    <td style="width:10%"><b><?= $activeOutstandingInvoicesCount; ?></b></td>
    <td style="width:10%"><b><?= Yii::$app->formatter->asCurrency($activeOutstandingInvoicesSum); ?></b></td>
  </tr>
  <tr>
    <td style="width:80%"><b>Inactive Outstanding Invoices</b></td>
    <td style="width:10%"><b><?= $inactiveOutstandingInvoicesCount; ?></b></td>
    <td style="width:10%"><b><?= Yii::$app->formatter->asCurrency($inactiveOutstandingInvoicesSum); ?></b></td>
  </tr>
  <tr>
    <td style="width:80%"><b>Number of Active Customers</b></td>
    <td style="width:10%"><b><?= $numberOfActiveCustomers; ?></b></td>
    <td style="width:10%"></td>
  </tr>
  <tr>
    <td style="width:80%"><b>Number of Active Enrolments</b></td>
    <td style="width:10%"><b><?= $numberOfEnrolments; ?></b></td>
    <td style="width:10%"></td>
  </tr>
</table>
<?php LteBox::end()?>
<?php Pjax::end(); ?>
