<?php

use common\models\User;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'birth_date')->textInput() ?>

    <?php echo $form->field($model, 'customer_id')->dropDownList(ArrayHelper::map(User::findByRole(User::ROLE_CUSTOMER),'id','userProfile.fullName')) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
