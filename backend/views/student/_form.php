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

<div class="student-form">
	<?php
	$session = Yii::$app->session;
	$locationId = $session->get('location_id');
	?>
    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row-fluid">
        <div class="col-md-4">
        <?php echo $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?php echo $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row-fluid">
        <div class="col-md-4">
            <?php echo $form->field($model, 'birth_date')->widget(DatePicker::classname(),[
        			'type' => DatePicker::TYPE_COMPONENT_APPEND,
        			'pluginOptions' => [
            		    'format' => 'mm-dd-yy',
                		'todayHighlight' => true,
        				'autoclose'=>true
            		]
        			]);?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
