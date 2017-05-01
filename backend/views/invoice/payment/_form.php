<?php

use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class=" p-10">
<?php $form = ActiveForm::begin([
    'id' => 'payment-edit-form',
	'action' => Url::to(['payment/edit', 'id' => $model->id]),
	'enableAjaxValidation' => true,
	'enableClientValidation' => false
]); ?>
   <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'amount')->textInput();?>
        </div>
        <div class="clearfix"></div>
	   <div class="col-md-6 form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default payment-cancel']);?>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>