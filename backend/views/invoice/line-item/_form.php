<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
@media (min-width: 768px) {
  .item-view dt {
    float: left;
    width: 110px;
    overflow: hidden;
    clear: left;
    text-align: right;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .item-view dd {
    margin-left: 130px;
  }
}
</style>
<div class="lesson-qualify p-10">

<?php $form = ActiveForm::begin([
    'id' => 'line-item-edit-form',
	'action' => Url::to(['invoice-line-item/update', 'id' => $model->id]),
	'enableClientValidation' => true
]); ?>
    <div id="item-edit-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <dl class="dl-horizontal">
        <div class="row" style="height: 35px">
            <div class="col-md-12">
                <dt>Code</dt>
                <dd><?= $model->code ?></dd>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <dt style="padding-top:6px;">Description</dt>
                <dd><?= $form->field($model, 'description')->textarea()->label(false);?></dd>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7">
            <dt style="padding-top:6px;">Price</dt>
            <dd><?= $form->field($model, 'amount')->textInput(['id' => 'amount-line'])->label(false);?></dd>
            </div>
            <div class="col-md-5">
                <dl class="item-view">
                    <dt style="padding-top:7px;">Cost</label></dt>
                    <dd><?= $form->field($model, 'cost')->textInput()->label(false);?></dd>
                </dl>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <dt style="padding-top:6px;">Quantity</dt>
                <dd><?= $form->field($model, 'unit')->textInput(['id' => 'unit-line'])->label(false);?></dd>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <dt></dt>
                <dd><?= $form->field($model, 'royaltyFree')->checkbox();?></dd>
            </div>
        </div>
    </dl>
    <?php ActiveForm::end(); ?>
</div>
	

<script>
    
</script>