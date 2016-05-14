<?php

use common\models\User;
use common\models\Program;
use common\models\Enrolment;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="enrolment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'programId')->dropDownList(ArrayHelper::map(Program::find()->active()->all(), 'id', 'name')); ?>

    <?php echo $form->field($model, 'teacherId')->dropDownList(ArrayHelper::map(User::findByRole(User::ROLE_TEACHER),'id','userProfile.fullName')) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
