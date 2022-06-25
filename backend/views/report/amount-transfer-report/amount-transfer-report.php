<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Html;
use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use Yii;
use common\models\User;
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
        'rowOptions' =>  ['class' => 'amount-transfer-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            'content' =>
                    Html::a('<i class="fa fa-print"></i>', '#', 
                    ['id' => 'print', 'class' => 'btn btn-default']),
            '{export}',
            '{toggleData}',
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
                'id' => 'amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>

<?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $paidPastLessondataProvider,
        'rowOptions' =>  ['class' => 'amount-transfer-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            'content' =>
                    Html::a('<i class="fa fa-print"></i>', '#', 
                    ['id' => 'print', 'class' => 'btn btn-default']),
            '{export}',
            '{toggleData}',
            // [
            //     'content' =>  $this->render('_button', ['searchModel' => $searchModel]),
            //     'options' => ['title' => 'Filter',]
            // ],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Paid Past Lessons(Service not taken)',
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
                'id' => 'amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>

<?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $invoicedataProvider,
        'rowOptions' =>  ['class' => 'amount-transfer-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            'content' =>
                    Html::a('<i class="fa fa-print"></i>', '#', 
                    ['id' => 'print', 'class' => 'btn btn-default']),
            '{export}',
            '{toggleData}',
            // [
            //     'content' =>  $this->render('_button', ['searchModel' => $searchModel]),
            //     'options' => ['title' => 'Filter',]
            // ],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Outstanding Invoices',
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
                'id' => 'amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>

<?php Pjax::begin(['id' => 'locations-listing']); ?>
    <?= KartikGridView::widget([
        'dataProvider' => $customerWithCreditdataProvider,
        'rowOptions' =>  ['class' => 'amount-transfer-report-detail-view'],
        'tableOptions' => ['class' => 'table table-condensed table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'toolbar' =>  [
            'content' =>
                    Html::a('<i class="fa fa-print"></i>', '#', 
                    ['id' => 'print', 'class' => 'btn btn-default']),
            '{export}',
            '{toggleData}',
            // [
            //     'content' =>  $this->render('_button', ['searchModel' => $searchModel]),
            //     'options' => ['title' => 'Filter',]
            // ],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Customers With Credit',
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
                'id' => 'amount-report'
            ]
            ],
]);

    ?>
<?php Pjax::end(); ?>
