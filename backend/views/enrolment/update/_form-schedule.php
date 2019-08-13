<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\jui\DatePicker;
use kartik\grid\GridView;
use yii\widgets\Pjax;

?>
<div id="enrolment-edit-end-date" style="display: none;" class="alert-danger alert fade in"></div>
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
                'firstDay' => 1,
                'changeYear' => true
            ]
            ])->label(false); 
        ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php Pjax::begin([
    'id' => 'after-end-date-changed-listing',
    'timeout' => 6000,
]); ?>
<?php
$columns = [
    [
        'label' => 'Objects',
        'attribute' => 'objects',
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'label' => 'Action',
        'attribute' => 'action',
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
    [
        'label' => 'Date Range',
        'attribute' => 'date_range',
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ]
];
?>

<?php if ($action === 'shrink') : ?>
    <label>Enrolment End Preview</label>
    <div class="row">
        <div class="col-lg-12">
            <?= GridView::widget([
                'dataProvider' => $previewDataProvider,
                'columns' => $columns,
                'summary' => false,
                'emptyText' => false
            ]); ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($action === 'extend') : ?>
    <label>Enrolment Extend Preview</label>
    <div class="row">
        <div class="col-lg-12">
            <?= GridView::widget([
                'dataProvider' => $previewDataProvider,
                'columns' => $columns,
                'summary' => false,
                'emptyText' => false
            ]); ?>        
        </div>
    </div>
<?php endif; ?>

<?php Pjax::end(); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">End Date Adjustment</h4>');
        $('.modal-save').show();
        $('.modal-save').text('Confirm');
        $('#popup-modal .modal-dialog').css({'width': '600px'});
    });

    $(document).on('change', '#course-enddate', function () {
        $('#modal-spinner').show();
        var endDate = $(this).val();
        var url = '<?= Url::to(['enrolment/edit-end-date', 'id' => $model->id]); ?>&endDate=' + endDate;
        $.pjax.reload({url: url, container: "#after-end-date-changed-listing", replace: false, async: false, timeout: 4000});
        return false;
    });

    $(document).on('pjax:complete', function(event) {
        $('#modal-spinner').hide();
    });

    $(document).on('modal-error', function (event, params) {
        if (params.error) {
            $('#enrolment-edit-end-date').html(params.error).fadeIn().delay(5000).fadeOut();
        }
    });
</script>