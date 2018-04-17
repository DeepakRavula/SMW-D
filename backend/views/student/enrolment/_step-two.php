<?php

use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
?>

<div class="row user-create-form">
    <div class="col-md-4">
        <?= $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
                'type' => DepDrop::TYPE_SELECT2,
                'pluginOptions' => [
                    'depends' => ['course-programid'],
                    'url' => Url::to(['course/teachers']),
                ]
        ]); ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'startDate')->textInput(['readOnly' => true])->label('Start Date');?>
    </div>
    <div class="col-md-2">
        <?= $form->field($courseSchedule, 'day')->textInput(['readOnly' => true])->label('Day');?>
    </div>
        <?= $form->field($courseSchedule, 'fromTime')->hiddenInput()->label(false);?>
    <div class="col-md-12">
        <div id="enrolment-create-calendar"></div>
    </div>
    <div class="pull-right m-t-10">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default private-enrol-cancel']); ?>
        <button class="btn btn-info enrolment-save-btn" type="submit" >Preview Lessons</button>
    </div>
    <div class="pull-left m-t-10">
        <button class="btn btn-info step2-back" type="submit" >Back</button>
    </div>
</div>

<script>
    $(document).on('click', '.enrolment-save-btn', function () {
        $('#private-enrolment-spinner').show();
    });

    $(document).on('week-view-calendar-select', function(event, params) {
        $('#course-startdate').val(moment(params.date, "DD-MM-YYYY h:mm a").format('MMM D, Y hh:mm A')).trigger('change');
        $('#courseschedule-day').val(moment(params.date, "DD-MM-YYYY h:mm a").format('dddd')).trigger('change');
        $('#courseschedule-fromtime').val(moment(params.date, "DD-MM-YYYY h:mm a").format('HH:mm:ss')).trigger('change');
        return false;
    });
</script>
