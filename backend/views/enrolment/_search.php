<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EnrolmentSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="enrolment-search">

	<?php
	$form = ActiveForm::begin([
				'action' => ['index'],
				'method' => 'get',
	]);
	?>

	<?php echo $form->field($model, 'id') ?>

	<?php echo $form->field($model, 'student_id') ?>

	<?php echo $form->field($model, 'qualification_id') ?>

	<?php // echo $form->field($model, 'length')  ?>

    <div class="form-group">
		<?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
		<?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>
