<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\jui\DatePicker;
use kartik\grid\GridView;

?>

<?php
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['enrolment/edit-end-date', 'id' => $model->id]),
    ]);
?>

<div class="row">
    <div class="col-md-3 text-center">
        <label>End Date</label>
    </div>
    <div class="col-md-3">
        <?= $form->field($course, 'endDate')->widget(DatePicker::classname(), [
            'value'  => Yii::$app->formatter->asDate($course->endDate),
            'dateFormat' => 'php:M d, Y',
            'options' => [
                'class' => 'form-control'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => '1500:3000',
                'changeYear' => true
            ]
            ])->label(false); 
        ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php yii\widgets\Pjax::begin([
    'id' => 'after-end-date-changed-listing',
    'timeout' => 6000,
]) ?>
    
<?php $lessonColumns = [
    [
        'label' => 'Date/Time',
        'value' => function ($model, $key, $index, $widget) {
            return (new \DateTime($model->date))->format('M d, Y H:i A');
        },
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column', 'style' => 'width:150px;'],
    ],
    [
        'label' => 'Duration',
        'value' => function ($model, $key, $index, $widget) {
            return (new \DateTime($model->duration))->format('H:i');
        },
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column', 'style' => 'width:80px;'],
    
    ]
]; 

$paymentcycleColumns = [
    [
        'label' => 'Start Date',
        'value' => function ($model, $key, $index, $widget) {
            return (new \DateTime($model->startDate))->format('M d, Y');
        },
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'label' => 'End Date',
        'value' => function ($model, $key, $index, $widget) {
            return (new \DateTime($model->endDate))->format('M d, Y');
        },
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ]
];

$pfiColumns = [
    [
        'label' => 'Invoice Number',
        'attribute' => 'invoice_number',
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'label' => 'Due Date',
        'attribute' => 'due_date',
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ]
];
?>

<?php if ($deletableLessonDataProvider) : ?>
    <div class="row">
        <div class="col-lg-12">
            <label>Afftected Lessons (to delete)</label>
            <?= GridView::widget([
                'dataProvider' => $deletableLessonDataProvider,
                'columns' => $lessonColumns,
                'summary' => false,
                'emptyText' => 'No lessons afftected!'
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <label>Afftected Payment Cycles (to delete)</label>
            <?= GridView::widget([
                'dataProvider' => $deletablePaymentCyclesDataProvider,
                'columns' => $paymentcycleColumns,
                'summary' => false,
                'emptyText' => 'No payment cycles afftected!'
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <label>Afftected PFIs (to delete)</label>
            <?= GridView::widget([
                'dataProvider' => $deletablePfiDataProvider,
                'columns' => $pfiColumns,
                'summary' => false,
                'emptyText' => 'No PFIs afftected!'
            ]); ?>
        </div>
    </div>
<?php endif; ?>

<?php \yii\widgets\Pjax::end(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">End Date Adjustment</h4>');
        $('.modal-save').text('Confirm');
        $('#popup-modal .modal-dialog').css({'width': '600px'});
    });

    $(document).on('change', '#course-enddate', function () {
        var endDate = $(this).val();
        var url = '<?= Url::to(['enrolment/edit-end-date', 'id' => $model->id]); ?>&endDate=' + endDate;
        $.pjax.reload({url: url, container: "#after-end-date-changed-listing", replace: false, async: false, timeout: 4000});
        return false;
    });
</script>