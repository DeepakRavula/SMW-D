<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div id="invoice-line-item-modal" class="invoice-line-item-form p-l-20">
    <?php $form = ActiveForm::begin(); ?>
 	<div class="row">
        <div class="col-xs-3">
    		<?php echo $form->field($model, 'description')->textInput()?>
        </div>
        <div class="col-xs-3">
   			<?php echo $form->field($model, 'unit')->textInput()?>
        </div>
		<div class="col-xs-3">
   			<?php echo $form->field($model, 'amount')->textInput()?>
        </div>
	</div>
    <div class="form-group p-l-20">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>