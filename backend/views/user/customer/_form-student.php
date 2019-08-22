<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Student;
use yii\helpers\Url;
use yii\jui\DatePicker;
use kartik\select2\Select2;

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
        'action' => Url::to(['student/create', 'userId' => $customer->id]),
        'id' => 'modal-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validationUrl' => Url::to(['student/validate']),
        ]); ?>

    <div class="row">
		<?php echo $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
		 <?php
        $customerName = $model->isNewRecord ? $customer->userProfile->lastname : null;
        $customerFullName = $model->isNewRecord ? $customer->userProfile->fullName : null;
    ?>
		<?php echo $form->field($model, 'last_name')->textInput(['maxlength' => true, 'value' => $customerName]) ?>
        <?php echo $form->field($model, 'customer_id')->textInput(['maxlength' => true, 'value' => $customerFullName, 'readonly'=> true]) ?>
		<?php echo $form->field($model, 'birth_date')->widget(DatePicker::className(), [
                'dateFormat' => 'php:M d, Y',
                'options' => [
                    'class' => 'form-control',
                    'readOnly' => true
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'yearRange' => '-70:+0',
                    'changeYear' => true,
                ],
            ]);
				?>
        <?php $list = [0 => 'Not Specified', 1 => 'Male', 2 => 'Female']; ?>
        <?php $model->isNewRecord ? $model->gender = 0: $model->gender = $model->gender ;  ?>
        <?= $form->field($model, 'gender')->radioList($list); ?>
        </div>
	<?php echo $form->field($customer, 'id')->hiddenInput()->label(false); ?>
    <?php ActiveForm::end(); ?>
</div>

