<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div id="warning-notification" style="display:none;" class="alert-warning alert fade in"></div>
<div id="enrolment-enddate" style="display:none;" class="alert-danger alert fade in"></div>
<?php
$form = ActiveForm::begin([
        'id' => 'enrolment-enddate-form',
        'action' => Url::to(['enrolment/edit-end-date', 'id' => $model->id]),
    ]);

?>
<div class="row">
    <div class="col-md-6">
        <?php
        echo $form->field($course, 'endDate')->widget(DateTimePicker::classname(), [
            'options' => [
                'value' => Yii::$app->formatter->asDate($course->endDate),
            ],
            'layout' => '{input}{picker}',
            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'M d,yyyy',
                'startView' => 2,
                'minView' => 2,
            ]
        ]);

        ?>
    </div>
    <div id="loader" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
</div>
<div class="row">
 <div class="form-group col-md-12">
<div class="pull-right">
    <div class="col-md-5">
<?php echo Html::a('Cancel', '', ['class' => 'btn btn-default enrolment-enddate-cancel']); ?>
    </div>
    <div class="col-md-5">
<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button', 'id' => 'enrolment-enddate-save-btn']) ?>
    </div>
    </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
