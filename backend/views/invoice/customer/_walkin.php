<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php $form = ActiveForm::begin([
	'id' => 'modal-form',
	'action' => $userModel->isNewRecord ? Url::to(['invoice/create-walkin', 'id' => $model->id]) : Url::to(['invoice/edit-walkin', 'id' => $model->id])
]); ?>

    <div class="row col-md-12">
		<?= $form->field($userModel, 'firstname')->textInput(); ?>
		<?= $form->field($userModel, 'lastname')->textInput(); ?>
		<?= $form->field($userEmail, 'email')->textInput(['id' => 'walkin-email']); ?>
	</div>

<?php ActiveForm::end(); ?>