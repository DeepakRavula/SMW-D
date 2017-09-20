<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\imperavi\Widget;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form">
	<?php $model->content = $content;
	$model->to = $emails;
	$data = ArrayHelper::map(User::find()->orderBy('email')->notDeleted()->all(), 'email', 'email');
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
    <div id="spinner" class="spinner col-md-4 col-md-offset-4" style="display:none;">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
<span class="sr-only">Loading...</span>
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
<?php echo Html::submitButton(Yii::t('backend', 'Send'), ['class' => 'btn btn-info', 'name' => 'signup-button' , 'id' =>'mail-send-button' ,]) ?>
		</div>
		<div class="clearfix"></div>
    </div>
<?php ActiveForm::end(); ?>
</div>
<script>
$(document).ready(function() {
     $(document).on('click', '#mail-send-button', function (e) {
        $('#spinner').show();
    });
    });
</script>