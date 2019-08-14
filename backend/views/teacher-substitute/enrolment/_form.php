<?php

use kartik\select2\Select2;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<div class="receive-payment-form">

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['teacher-substitute/enrolment', 'EnrolmentSubstituteTeacher[enrolmentIds]' => $model->enrolmentIds])
    ]); ?>

    <div class="row">
        <div class="col-xs-7">
            <?= $form->field($model, 'teacherId')->widget(Select2::classname(), [
                'data' => $teachers,
                'options' => [
                    'placeholder' => 'Teacher'
                ]
            ])->label('Teacher'); ?>
        </div>
        <div class="col-xs-5">
            <?= $form->field($model, 'changesFrom')->widget(DatePicker::classname(), [
                'value'  => Yii::$app->formatter->asDate($model->changesFrom),
                'dateFormat' => 'php:M d, Y',
                'options' => [
                    'class' => 'form-control'
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'yearRange' => '2010:2080',
                    'changeYear' => true,
                    'firstDay' => 1,
                ]
            ])->label('Effect From'); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function () {
        $('.modal-save').show();
        $('.modal-save').text('Preview Lessons');
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Teacher Change</h4>');
        $('#popup-modal .modal-dialog').css({'width': '600px'});
    });
</script>