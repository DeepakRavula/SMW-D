<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Student;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form form-well form-well-smw">
	<?php
    $session = Yii::$app->session;
    $locationId = $session->get('location_id');
    ?>
    <?php $form = ActiveForm::begin($model->isNewRecord ? ['action' => Url::to(['/student/create'])] : [
		'action' => Url::to(['/student/update', 'id' => $model->id]),
		'id' => 'student-form'
		]); ?>

    <div class="row">
        <div class="col-xs-6">
            <?php echo $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-6">
             <?php
            $customerName = $model->isNewRecord ? $customer->userProfile->lastname : null;
        ?>
            <?php echo $form->field($model, 'last_name')->textInput(['maxlength' => true, 'value' => $customerName]) ?>
        </div>
        <div class="col-xs-6">
            <?php echo $form->field($model, 'birth_date')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class' => 'form-control'],
                ]); ?>
        </div>
			<div class="col-xs-6">
				<?php if (!$model->isNewRecord) : ?>
					<?php echo $form->field($model, 'status')->dropDownList(Student::statuses()) ?>
				<?php endif; ?>
			</div>
            <div class="clearfix"></div>
        </div>
	<?php echo $form->field($customer, 'id')->hiddenInput()->label(false); ?>
    <div class="row-fluid">
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
		<?php 
            if (!$model->isNewRecord) {
                echo Html::a('Cancel','#', ['class' => 'btn btn-default student-profile-cancel-button']);
            }
        ?>
    </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<script>
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
</script>