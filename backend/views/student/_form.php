<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form form-well form-well-smw">
	<?php
	$session = Yii::$app->session;
	$locationId = $session->get('location_id');
	?>
    <?php $form = ActiveForm::begin($model->isNewRecord ? ['action' => '/student/create'] : null); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row">
        <div class="col-xs-6">
            <div class="row-fluid">
            <?php echo $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="row-fluid">
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
        <div class="col-xs-6">
    		<div class="row-fluid">
                <?php
            $customerName = $model->isNewRecord ? $customer->userProfile->lastname : null;
        ?>
            <?php echo $form->field($model, 'last_name')->textInput(['maxlength' => true,'value' => $customerName]) ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
	<?php echo $form->field($customer, 'id')->hiddenInput()->label(false); ?>
    <div class="row-fluid">
    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

