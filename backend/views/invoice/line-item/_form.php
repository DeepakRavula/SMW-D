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
        <div class="row">
            <div class="col-md-12">
                <dt>Code</dt>
                <dd><?= $model->code ?></dd>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <dt>Description</dt>
                <dd><?= $form->field($model, 'description')->textarea()->label(false);?></dd>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7 text-right">
            <dt>Price</dt>
            <dd><?= $form->field($model, 'amount')->textInput(['id' => 'amount-line'])->label(false);?></dd>
            </div>
			<?php if(Yii::$app->user->can('administrator') || Yii::$app->user->can('owner')) :?>
            <div class="col-md-5 text-right">
                <dl class="item-view">
                    <dt>Cost</label></dt>
                    <dd><?= $form->field($model, 'cost')->textInput()->label(false);?></dd>
                </dl>
            </div>
			<?php endif;?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <dt>Quantity</dt>
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
    <div class="row">
        <div class="col-md-12">
            <div class="form-group pull-right">
                <?= Html::a('Cancel', '', ['class' => 'btn btn-default line-item-cancel']);?>
                <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
            </div>
            <div class="form-group pull-left">       
             <?= Html::a('Delete', [
                        'delete', 'id' => $model->id
                    ],
                    [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ]
                    ]); ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
	

