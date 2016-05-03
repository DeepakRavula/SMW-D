<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Program;

/* @var $this yii\web\View */
/* @var $model common\models\Qualification */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="qualification-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

	<?php echo $form->field($model, 'teacher_id')->dropDownList(ArrayHelper::map(User::findByRole(User::ROLE_TEACHER), 'id', 'userProfile.fullName')) ?>
	<?php echo $form->field($model, 'program_id')->dropDownList(ArrayHelper::map(Program::find()->active()->all(), 'id', 'name')) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
