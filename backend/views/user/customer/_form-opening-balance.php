<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div>
	<?php $form = ActiveForm::begin([
        'action' => Url::to(['customer/add-opening-balance', 'id' => $userModel->id]),
    ]); ?>
	<div class="row">
        <div class="col-xs-4">
			<?php echo $form->field($model, 'amount')->textInput(['class' => 'right-align form-control']); ?>
        </div>
	</div>
    <div class="row">
        <div class="col-md-12">
	<div class="pull-right">
        <?php echo Html::a('Cancel', '#', ['class' => 'btn btn-default ob-cancel']);?>
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
		
	</div>
        </div>
    </div>
	<?php ActiveForm::end(); ?>
</div>