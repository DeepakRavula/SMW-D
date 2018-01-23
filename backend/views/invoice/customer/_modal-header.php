<?php

use yii\bootstrap\ActiveForm;

?>
<style>
	.modal-header {
		padding:10px;
	}
</style>
<h4 class="pull-left">Add Customer</h4>
<?php $form = ActiveForm::begin([
    'method' => 'post',
    'id' => 'customer-form',
]); ?>
<div class="pull-right">
	<?=	 $form->field($model, 'userName')->textInput(['class' => 'h-30 m-l-15 form-control', 'placeholder' => 'Search'])->label(false);?>
</div>
    <?php ActiveForm::end(); ?>