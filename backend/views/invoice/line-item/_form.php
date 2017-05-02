<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\switchinput\SwitchInput;
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
        <div class="col-md-4">
            <?= $form->field($model, 'code')->textInput();?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'unit')->textInput();?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'cost')->textInput();?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'netPrice')->textInput(['readOnly' => true])->label('Net Price');?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'amount')->textInput()->label('Base Price');?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'discount')->textInput()->label('Discount');?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'discountType')->widget(SwitchInput::classname(),
                [
                'name' => 'discountType',
                'pluginOptions' => [
                    'handleWidth' => 30,
                    'onText' => '%',
                    'offText' => '$',
                ],
            ])->label('Discount Type');?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'taxStatus')->dropDownList(ArrayHelper::map(
                            TaxStatus::find()->all(), 'id', 'name'
            ), ['prompt' => 'Select']);?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'isRoyalty')->widget(SwitchInput::classname(),
                [
                'name' => 'isRoyalty',
                'pluginOptions' => [
                    'handleWidth' => 30,
                    'onText' => 'Yes',
                    'offText' => 'No',
                ],
            ])->label('Is Royalty');?>
        </div>
       <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea();?>
        </div>
    <div class="col-md-12 p-l-20 form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
        
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default line-item-cancel']);?>
        <?= Html::a('Delete', [
            'delete', 'id' => $model->id
        ],
        [
            'class' => 'btn btn-primary',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ]
        ]); ?>
        <div class="clearfix"></div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>
