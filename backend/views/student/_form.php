<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Student;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="row user-create-form">
	<?php
    $session = Yii::$app->session;
    $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
    ?>
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['/student/update', 'id' => $model->id]),
        'id' => 'student-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validationUrl' => Url::to(['student/validate', 'id' => $model->id]),
        ]); ?>

    <div class="row">
            <?php echo $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
             <?php
            $customerName = $model->isNewRecord ? $customer->userProfile->lastname : null;
            $model->birth_date = !empty($model->birth_date) ? (new \DateTime($model->birth_date))->format('d-m-Y') : null;
        ?>
            <?php echo $form->field($model, 'last_name')->textInput(['maxlength' => true, 'value' => $customerName]) ?>
            <?php echo $form->field($model, 'birth_date')->textInput()?>
				<?php if (!$model->isNewRecord) : ?>
					<?php echo $form->field($model, 'status')->dropDownList(Student::statuses()) ?>
				<?php endif; ?>
        </div>
	<?php echo $form->field($customer, 'id')->hiddenInput()->label(false); ?>
    <div class="row">
    <div class="pull-right">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default student-profile-cancel-button']);
        ?>
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
$(document).ready(function() {
$.fn.datepicker.noConflict();
$('#student-birth_date').datepicker({
   altField: '#student-birth_date',
   altFormat: 'dd-mm-yy',
   changeMonth: true,
   changeYear: true,
   yearRange : '-70:today',
   onChangeMonthYear:function(y, m, i){
       var d = i.selectedDay;
       $(this).datepicker('setDate', new Date(y, m-1, d));
   }
});
});
</script>