<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Student;
use yii\helpers\Url;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="row bulk-reschedule-form">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        ]); ?>

<div class="col-md-6">
    <?= $form->field($model, 'date')->widget(
        DatePicker::classname(), [
            'dateFormat' => 'php:M d, Y',
            'options' => [
                'class' => 'form-control'
            ],
            'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => '1500:3000',
                'changeYear' => true
            ]
        ]);
    ?>
</div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function() {
        $('#popup-modal .modal-dialog').css({'width' : '400'})
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Bulk Reschedule</h4>');
    });
</script>