<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="form-well p-l-20 payments-form p-t-15">

	<?php $form = ActiveForm::begin(); ?>
	<div class="row">
        <div class="col-xs-4">
			<?php echo $form->field($model, 'amount')->textInput(array('placeholder' => 'Amount'))->label(false); ?>
        </div>
		<div class="col-xs-4">
            <?php echo $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class'=>'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '-70:today' 
                    ]
                ]); ?>
        </div>
	</div>
	<div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php
		if (!$model->isNewRecord) {
			echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
		}
		?>
	</div>
	<?php ActiveForm::end(); ?>
</div>