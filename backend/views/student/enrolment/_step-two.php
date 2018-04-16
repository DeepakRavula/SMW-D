<?php

use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
use yii\jui\DatePicker;
//use kartik\date\DatePickerAsset;

//DatePickerAsset::register($this);
?>
<div class="row user-create-form">
<div class="col-md-4">
<?php
    // Dependent Dropdown
    echo $form->field($model, 'teacherId')->widget(
        DepDrop::classname(),
        [
          'type' => DepDrop::TYPE_SELECT2,
        'pluginOptions' => [
            'depends' => ['course-programid'],
            'url' => Url::to(['course/teachers']),
        ],
    ]
    );
    ?>
</div>
<div class="col-md-4">
	<?php
    echo $form->field($model, 'startDate')->widget(DatePicker::className(), [
                'dateFormat' => 'MMM dd,yyyy',
                'clientOptions' => [
                    'changeMonth' => true,
                    'yearRange' => '1500:3000',
                    'changeYear' => true,
                ],
            ])->textInput(['placeholder' => 'Select Date','value'=>date("M d,Y")]);
    ?>
</div>
<div class="col-md-4">
	<?= $form->field($courseSchedule, 'day')->textInput(['readOnly' => true])->label('Day');?>
</div>
<div class="clearfix"></div>
<?= $form->field($courseSchedule, 'fromTime')->hiddenInput()->label(false);?>
<?= $this->render('_calendar'); ?>
<div class="clearfix"></div>
<div class="pull-right m-t-10">
	<?= Html::a('Cancel', '#', ['class' => 'btn btn-default private-enrol-cancel']); ?>
	<button class="btn btn-info enrolment-save-btn" type="submit" >Preview Lessons</button>
</div>
<div class="pull-left m-t-10">
	<button class="btn btn-info step2-back" type="submit" >Back</button>
</div>
</div>
<script>
$(document).ready(function() {
$(document).on('click', '.enrolment-save-btn', function () {
    $('#private-enrolment-spinner').show();
});
});
</script>