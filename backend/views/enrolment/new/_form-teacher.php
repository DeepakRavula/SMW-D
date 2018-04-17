<?php

use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
?>

<div class="row user-create-form">
    <div class="col-md-4">
        <?= $form->field($model, 'teacherId')->widget(
            DepDrop::classname(),
            [
              'type' => DepDrop::TYPE_SELECT2,
            'pluginOptions' => [
                'depends' => ['course-programid'],
                'url' => Url::to(['course/teachers']),
            ]
        ]); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'startDate')->textInput(['readOnly' => true])->label('Start Date'); ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($courseSchedule, 'day')->textInput(['readOnly' => true])->label('Day');?>
    </div>
    <?= $form->field($courseSchedule, 'fromTime')->hiddenInput()->label(false);?>
    <div class="col-md-12">
        <div id="reverse-enrolment-calendar"></div>
    </div>
    <div class="pull-right m-t-10">
        <?= Html::a('Cancel', '#', ['class' => 'm-r-10 btn btn-default new-enrol-cancel']); ?>
        <button class="step2-next btn btn-info pull-right" type="button" >Next</button>
    </div>
    <div class="pull-left m-t-10">
        <button class="btn btn-info step2-back" type="submit" >Back</button>
    </div>
</div>
