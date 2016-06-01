<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form form-well">
    <h4>Add new student</h4>
	<?php
	$session = Yii::$app->session;
	$locationId = $session->get('location_id');
	?>
    <?php $form = ActiveForm::begin($model->isNewRecord ? ['action' => '/student/create'] : null); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row">
        <div class="col-md-4">
        <?php echo $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?php echo $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?php echo $form->field($model, 'birth_date')->widget(\yii\jui\DatePicker::classname(), [
                'options' => ['class'=>'form-control'],
				'clientOptions' => [
					'changeMonth' => true,
					'changeYear' => true,
					'yearRange' => '-70:-4'	
				]
]); ?>
        </div>
        <div class="clearfix"></div>
    </div>
	<?php echo $form->field($customer, 'id')->hiddenInput()->label(false); ?>
    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>

