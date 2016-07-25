<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="payments-form">

    <?php $form = ActiveForm::begin(); ?>
 	<div class="row">
        <div class="col-xs-4">
    		<?php echo $form->field($model, 'payment_method_id')->textInput() ?>
        </div>
        <div class="col-xs-4">
   			<?php echo $form->field($model, 'amount')->textInput() ?>
        </div>
	</div>
</div>
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php 
			if(! $model->isNewRecord){
				echo Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	
			}
		?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
