<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="payments-form p-l-20">
    <?php $form = ActiveForm::begin(); ?>
 	<div class="row">
        <div class="col-xs-3">
   			<?php echo $form->field($model, 'amount')->textInput() ?>
        </div>
		<?php echo $form->field($model, 'payment_method_id')->hiddenInput()->label(false); ?>
	</div>
    <div class="row">
    <div class="col-md-12">
        <div class="pull-right">
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
			<?php 
            if (!$model->isNewRecord) {
                echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-default']);
            }
        ?>
    </div>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
