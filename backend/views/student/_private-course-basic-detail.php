<?php

use common\models\Program;
use common\models\Enrolment;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use wbraganca\selectivity\SelectivityWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php
$privatePrograms = ArrayHelper::map(Program::find()
		->active()
		->where(['type' => Program::TYPE_PRIVATE_PROGRAM])
		->all(),
	'id', 'name')
?>
<div class="enrolment-form form-well form-well-smw">
<?php $form = ActiveForm::begin(); ?>
    <div class="row">
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'startDate')->widget(DatePicker::classname(), [
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
					'options' => [
						'value' => (new \DateTime())->format('d-m-Y'),
					],
       				'pluginOptions' => [
					'format' => 'dd-mm-yyyy',
					'todayHighlight' => true,
					'autoclose' => true
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'duration')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'showMeridian' => false,
					'defaultTime' => date('H:i', strtotime('00:30')),
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?=
				$form->field($model, 'programId')->widget(SelectivityWidget::classname(), [
					'pluginOptions' => [
						'allowClear' => true,
						'multiple' => false,
						'items' => $privatePrograms,
						'placeholder' => 'Select Program'
					]
				]);
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
        	<?= $form->field($model, 'paymentFrequency')->radioList(Enrolment::paymentFrequencies())?>
		</div>
	</div>
	<?php ActiveForm::end(); ?>

</div>
<?php
    $session = Yii::$app->session;
	$session->set('startDate', $model->startDate);
	$session->set('duration', $model->duration);
	$session->set('programId', $model->programId);
?>
