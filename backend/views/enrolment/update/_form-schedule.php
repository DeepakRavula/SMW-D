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

<?php if ($action === 'shrink') : ?>
    <div class="row">
        <div class="col-lg-12">
            <label>Lessons will be deleted within the date daterange <?= $dateRange ?> due to enrollment end</label>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <label>Payment cycles will be deleted within the date daterange <?= $dateRange ?> due to enrollment end</label>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <label>PFIs will be deleted within the date daterange <?= $dateRange ?> due to enrollment end</label>
        </div>
    </div>
<?php endif; ?>

<?php if ($action === 'extend') : ?>
    <div class="row">
        <div class="col-lg-12">
            <label>New lessons will be created within the date daterange <?= $dateRange ?> due to extend end date</label>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <label>New payment cycles will be created within the date daterange <?= $dateRange ?> due to extend end date</label>
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