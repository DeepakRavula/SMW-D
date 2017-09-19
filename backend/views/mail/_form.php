<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\imperavi\Widget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form">
	<?php $model->content = $content;
	$model->to = $emails;
	?>
	<?php $form = ActiveForm::begin([
		'id' => 'mail-form',
		'action' => Url::to(['email/send'])
	]);
	?>
	<div class="row">
        <div class="col-lg-12">
			<?php
			echo $form->field($model, 'to')->widget(Select2::classname(), [
				'data' => $data,
				'pluginOptions' => [
					'tags' => true,
					'allowClear' => true,
					'multiple' => true,
				],
			]);
			?>
        </div>
	</div>
	<div class="row">
        <div class="col-lg-12">
			<?php echo $form->field($model, 'subject')->textInput(['value' => $subject]) ?>
        </div>
	</div>
	<div class="row">
        <div class="col-lg-12">
			<?php
			echo $form->field($model, 'content')->widget(Widget::className(), [
				'plugins' => ['table'],
				'options' => [
					'minHeight' => 400,
					'maxHeight' => 400,
					'buttonSource' => true,
					'convertDivs' => false,
					'removeEmptyTags' => false,
				]
				]
			);
			?>

        </div>
	</div>
    <div class="row-fluid">
		<div class="form-group col-lg-6">
<?php echo Html::submitButton(Yii::t('backend', 'Send'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		</div>
		<div class="clearfix"></div>
    </div>
<?php ActiveForm::end(); ?>
</div>