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

<?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $paidFutureLessondataProvider,
        'rowOptions' =>  ['class' => 'change-over-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            // 'content' =>
            //         Html::a('<i class="fa fa-print"></i>', '#', 
            //         ['id' => 'print', 'class' => 'btn btn-default']),
            // '{export}',
            // '{toggleData}',
            // [
            //     'content' =>  $this->render('_button', ['searchModel' => $searchModel]),
            //     'options' => ['title' => 'Filter',]
            // ],
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

<?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $paidPastLessondataProvider,
        'rowOptions' =>  ['class' => 'change-over-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            // 'content' =>
            //         Html::a('<i class="fa fa-print"></i>', '#', 
            //         ['id' => 'print', 'class' => 'btn btn-default']),
            // '{export}',
            // '{toggleData}',
            // [
            //     'content' =>  $this->render('_button', ['searchModel' => $searchModel]),
            //     'options' => ['title' => 'Filter',]
            // ],
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

<?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $activeInvoicedataProvider,
        'rowOptions' =>  ['class' => 'change-over-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            // 'content' =>
            //         Html::a('<i class="fa fa-print"></i>', '#', 
            //         ['id' => 'print', 'class' => 'btn btn-default']),
            // '{export}',
            // '{toggleData}',
            // [
            //     'content' =>  $this->render('_button', ['searchModel' => $searchModel]),
            //     'options' => ['title' => 'Filter',]
            // ],
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
                    $balance = (round($data->balance, 2) > 0.00 && round($data->balance, 2) <= 0.09) || (round($data->balance, 2) < 0.00 && round($data->balance, 2) >= -0.09)  ? Yii::$app->formatter->format(round('0.00', 2), ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                ]]): Yii::$app->formatter->format(round($data->balance, 2), ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                ]]);
                    return  Yii::$app->formatter->asCurrency(round($balance, 2));
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

<?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $inactiveInvoicedataProvider,
        'rowOptions' =>  ['class' => 'change-over-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            // 'content' =>
            //         Html::a('<i class="fa fa-print"></i>', '#', 
            //         ['id' => 'print', 'class' => 'btn btn-default']),
            // '{export}',
            // '{toggleData}',
            // [
            //     'content' =>  $this->render('_button', ['searchModel' => $searchModel]),
            //     'options' => ['title' => 'Filter',]
            // ],
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
                    return  Yii::$app->formatter->asCurrency(round($data->balance, 2));
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

<?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $activeCustomerWithCreditdataProvider,
        'rowOptions' =>  ['class' => 'change-over-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            // 'content' =>
            //         Html::a('<i class="fa fa-print"></i>', '#', 
            //         ['id' => 'print', 'class' => 'btn btn-default']),
            // '{export}',
            // '{toggleData}',
            // [
            //     'content' =>  $this->render('_button', ['searchModel' => $searchModel]),
            //     'options' => ['title' => 'Filter',]
            // ],
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

<?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $inactiveCustomerWithCreditdataProvider,
        'rowOptions' =>  ['class' => 'change-over-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            // 'content' =>
            //         Html::a('<i class="fa fa-print"></i>', '#', 
            //         ['id' => 'print', 'class' => 'btn btn-default']),
            // '{export}',
            // '{toggleData}',
            // [
            //     'content' =>  $this->render('_button', ['searchModel' => $searchModel]),
            //     'options' => ['title' => 'Filter',]
            // ],
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
<dl class="horizontal">
	<dt class=" m-r-10">Prepaid Lessons Paid Amount Total</dt>
	<dd class = "total-horizontal-dd pull-right"><?= Yii::$app->formatter->asCurrency($paidFutureLessonsSum); ?></dd>
    <dt class=" m-r-10">Paid Unscheduled Lessons</dt>
	<dd class = "total-horizontal-dd pull-right"><?= Yii::$app->formatter->asCurrency($paidPastLessonsSum); ?></dd>
    <dt class=" m-r-10">Active Outstanding Invoices Balance Total</dt>
	<dd class = "total-horizontal-dd pull-right"><?= Yii::$app->formatter->asCurrency($activeOutstandingInvoicesSum); ?></dd>
    <dt class=" m-r-10">Inactive Outstanding Invoices Balance Total</dt>
	<dd class = "total-horizontal-dd pull-right"><?= Yii::$app->formatter->asCurrency($inactiveOutstandingInvoicesSum); ?></dd>
    <dt class=" m-r-10">Prepaid Future Lessons Count</dt>
	<dd class = "total-horizontal-dd pull-right"><?= $paidFutureLessonsCount; ?></dd>
    <dt class=" m-r-10">Paid Unscheduled Lessons Count</dt>
	<dd class = "total-horizontal-dd pull-right"><?= $paidPastLessonsCount; ?></dd>
    <dt class=" m-r-10">Active Outstanding Invoices Count</dt>
	<dd class = "total-horizontal-dd pull-right"><?= $activeOutstandingInvoicesCount; ?></dd>
    <dt class=" m-r-10">Inactive Outstanding Invoices Count</dt>
	<dd class = "total-horizontal-dd pull-right"><?= $inactiveOutstandingInvoicesCount; ?></dd>
    <dt class=" m-r-10">Number of active Customers</dt>
	<dd class = "total-horizontal-dd pull-right"><?= $numberOfActiveCustomers; ?></dd>
    <dt class=" m-r-10">Number of active Enrolments</dt>
	<dd class = "total-horizontal-dd pull-right"><?= $numberOfEnrolments; ?></dd>
    <dt class=" m-r-10">Amount To Be Transferred</dt>
    <dd class = "total-horizontal-dd pull-right"><?= Yii::$app->formatter->asCurrency($paidFutureLessonsSum + $paidPastLessonsSum); ?></dd>
    <dt class=" m-r-10">(Prepaid Lessons Paid Amount Total + Past Unscheduled Lessons Amount Total)</dt>

</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>