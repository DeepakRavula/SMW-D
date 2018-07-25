<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="payments-form p-l-20">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['proforma-invoice/update', 'id' => $model->id]),
    ]); ?>
 	<div class="row">
        <div class="col-xs-7">
            <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
                'options' => [
                    'class' => 'form-control',
                    'readOnly' => true
                ],
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                    'defaultDate' => (new \DateTime($model->date))->format('M d, Y'),
                    'changeMonth' => true,
                    'yearRange' => '2015:2100',
                    'changeYear' => true,
                ]
                ])->label('Date');
            ?>
        </div>
        <div class="col-xs-7">
            <?= $form->field($model, 'dueDate')->widget(DatePicker::classname(), [
                'options' => [
                    'class' => 'form-control',
                    'readOnly' => true
                ],
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                    'defaultDate' => (new \DateTime($model->dueDate))->format('M d, Y'),
                    'changeMonth' => true,
                    'yearRange' => '2015:2100',
                    'changeYear' => true,
                ]
                ])->label('Due Date');
            ?>
        </div>
	</div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function () {
        $('.modal-save').show();
        $('#popup-modal .modal-dialog').css({'width': '400px'});
        $('#popup-modal').find('.modal-header').html('Edit Details');
    });
    $(document).on('modal-success', function(event, params) {
        $.pjax.reload({container: "#invoice-details", replace: false, timeout: 4000});
        return false;
    });
</script>