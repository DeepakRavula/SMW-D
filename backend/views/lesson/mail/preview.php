<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use common\models\InvoiceLineItem;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form">
    <?php $form = ActiveForm::begin([
		'action' => Url::to(['lesson/send-mail', 'id' => $model->id])
	]); ?>
		<div class="row">
        <div class="col-lg-12">
			<?php 
			$email = !empty($model->enrolment->student->customer->email) ? $model->enrolment->student->customer->email : null;
			$subject = $model->course->program->name . ' lesson reschedule';
			$body = null;
			?>
			<?php if($model->isRescheduled()) : ?>
        	<?php $body = $model->course->program->name . ' lesson has been rescheduled. Kindly verify your lesson details given below'; 
			?>
			<?php endif; ?>
			<?php $content = $this->render('content', [
				'toName' => !empty($model->enrolment->student->customer->publicIdentity) ? $model->enrolment->student->customer->publicIdentity : null,
				'content' => $body,
				'model' => $model,
			]); ?>
            <?php echo $form->field($model, 'toEmailAddress')->textInput(['value' => $email, 'readonly' => true]) ?>
        </div>
        </div>
		<div class="row">
        <div class="col-lg-12">
            <?php echo $form->field($model, 'subject')->textInput(['value' => $subject]) ?>
        </div>
        </div>
		<div class="row">
        <div class="col-lg-12">
            <?php echo $form->field($model, 'content')->widget(CKEditor::className(), [
        		'options' => [
					'value' => $content,
					'rows' => 6],
        		'preset' => 'full',
    		]) ?>
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
