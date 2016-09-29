<?php

use common\models\Course;
use common\models\Enrolment;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?= $this->render('_view-enrolment',[
	'model' => $model->enrolment,
]);?>
<div class="enrolment-form form-well form-well-smw">
	<?php $form = ActiveForm::begin(); ?>
    <div class="row">
		<?php
			$fromTime = Yii::$app->formatter->asTime($model->fromTime);
			$model->fromTime = ! empty($model->fromTime) ? $fromTime : null;
		?>
		<div class="col-md-4">
			<?= $form->field($model,'fromTime')->widget(TimePicker::classname(), []); ?>
		</div>
	</div>
    <div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>
