<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\TaxStatus;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-qualify p-10">

<?php $form = ActiveForm::begin([
    'id' => 'line-item-edit-form',
	'action' => Url::to(['invoice-line-item/update', 'id' => $model->id]),
	'enableAjaxValidation' => true,
	'enableClientValidation' => false
]); ?>
   <div class="row">
	   <div class="col-md-6">
			<?= $form->field($model, 'description')->textarea();?>
        </div>
	   <div class="col-md-6">
			<?= $form->field($model, 'unit')->textInput(['readOnly' => true]);?>
        </div>
	    <div class="col-md-6">
			<?= $form->field($model, 'amount')->textInput()->label('Base Price');?>
        </div>
	    <div class="col-md-6">
			<?= $form->field($model, 'discount')->textInput();?>
        </div>
	   <div class="col-md-6">
			<?= $form->field($model, 'tax_status')->dropDownList(ArrayHelper::map(
                            TaxStatus::find()->all(), 'id', 'name'
            ));?>
        </div>
	   <div class="col-md-6">
			<?= $form->field($model, 'isRoyalty')->textInput(['value' => $model->getRoyalty()]);?>
        </div>
    <div class="col-md-12 p-l-20 form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'button']) ?>
		<?= Html::a('Cancel', '', ['class' => 'btn btn-default line-item-cancel']);?>
		<div class="clearfix"></div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>
