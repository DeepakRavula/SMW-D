<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="clearfix"></div>

<div class="form-well p-l-20 payments-form p-t-15 m-t-20">
	<h4>Add new opening balance</h4>
	<?php $form = ActiveForm::begin([
		'action' => Url::to(['user/add-opening-balance', 'id' => $userModel->id])
	]); ?>
	<div class="row">
        <div class="col-xs-4">
			<?php echo $form->field($model, 'amount')->textInput(['placeholder' => 'Amount'])->label(false); ?>
        </div>
		<div class="col-xs-4">
            <?php echo $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class'=>'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '-70:today' 
                    ]
                ])->textInput(['placeholder' => 'Date'])->label(false); ?>
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