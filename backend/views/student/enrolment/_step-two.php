<?php

use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use yii\helpers\Html;
use kartik\date\DatePicker;
use kartik\date\DatePickerAsset;

DatePickerAsset::register($this);
?>
<div class="row user-create-form">
<div class="col-md-3 m-r-10">
<?php
    // Dependent Dropdown
    echo $form->field($model, 'teacherId',['horizontalCssClasses' => [
		'label' => '',
		'wrapper' => '',
]])->widget(DepDrop::classname(),
        [
		  'type' => DepDrop::TYPE_SELECT2,
        'options' => ['id' => 'course-teacherid'],
        'pluginOptions' => [
            'depends' => ['course-programid'],
            'placeholder' => 'Select...',
            'url' => Url::to(['course/teachers']),
        ],
    ]);
    ?>
</div>
<div class="col-md-4 m-r-10">
	<?php
	echo $form->field($model, 'startDate', ['horizontalCssClasses' => [
		'label' => '',
		'wrapper' => '',
],
		])->widget(DatePicker::classname(), [
		'type' => DatePicker::TYPE_INPUT,
		'options' => [
			'value' => (new \DateTime())->format('d-m-Y'),
		],
		'pluginOptions' => [
			'autoclose' => true,
			'format' => 'dd-mm-yyyy',
		],
	]);
	?>
</div>
<div class="col-md-3 m-r-10">
	<?= $form->field($courseSchedule, 'day', ['horizontalCssClasses' => [
		'label' => '',
		'wrapper' => '',
]])->textInput(['readOnly' => true])->label('Day');?>
</div>
</div>
<div class="clearfix"></div>
<?= $form->field($courseSchedule, 'fromTime')->hiddenInput()->label(false);?>
<?= $this->render('_calendar'); ?>
<div class="form-group pull-right">
	  <?= Html::a('Cancel', '#', ['class' => 'btn btn-default private-enrol-cancel']); ?>
	 <button class="btn btn-info nextBtn" type="button" >Save</button>
</div>
