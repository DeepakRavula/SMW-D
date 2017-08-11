<?php
use yii\helpers\Html;
use common\models\Invoice;
use common\models\Program;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;

use kartik\date\DatePickerAsset;
DatePickerAsset::register($this);
?>
<?php if (!empty($model)):?>
<?php $form = ActiveForm::begin([
	'id' => 'enrolment-enddate-form',
]); ?>
	<div class="col-md-3">
		<?php
		echo $form->field($model->course, 'endDate')->widget(DatePicker::classname(),
			[
			'options' => [
				'value' => (new \DateTime($model->course->endDate))->format('d-m-Y'),
			],
			'type' => DatePicker::TYPE_COMPONENT_APPEND,
			'pluginOptions' => [
				'autoclose' => true,
				'format' => 'dd-mm-yyyy'
			]
		]);
		?>
		</div>
<?php ActiveForm::end(); ?>
<?php endif; ?>