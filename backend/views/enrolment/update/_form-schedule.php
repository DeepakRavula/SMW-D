<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\jui\DatePicker;

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

<script type="text/javascript">
$(document).ready(function() {
    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">End Date Adjustment</h4>');
    $('.modal-save').text('Confirm');
    $('#popup-modal .modal-dialog').css({'width': '600px'});
});

$(document).on('change', '#course-enddate', function () {
        var url = '<?= Url::to(['enrolment/update', 'id' => $model->id, 'preview' => true]); ?>';
    });
</script>