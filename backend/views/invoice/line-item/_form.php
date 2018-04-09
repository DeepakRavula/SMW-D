<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify p-10">

<?php $form = ActiveForm::begin([
    'id' => 'modal-form',
    'action' => Url::to(['invoice-line-item/update', 'id' => $model->id])
]); ?>
    <div id="item-edit-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <dl class="dl-horizontal item-main-view">
        <div class="row item-code">
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
            <dd><?= $form->field($model, 'amount')->textInput(['class' => 'text-right form-control', 
                'id' => 'amount-line', 'value' => Yii::$app->formatter->asDecimal($model->amount, 2)])->label(false);?></dd>
            </div>
            <?php if (!$model->isOpeningBalance()) : ?>
			<?php if (Yii::$app->user->can('administrator') || Yii::$app->user->can('owner')) :?>
            <div class="col-md-5 text-right">
                <dl class="item-view">
                    <dt>Cost</label></dt>
                    <dd><?= $form->field($model, 'cost')->textInput(['class' => 'text-right form-control'])->label(false);?></dd>
                </dl>
            </div>
			<?php endif;?>
        </div>
        <div class="row">
            <div class="col-md-6">
                <dt>Quantity</dt>
                <dd><?= $form->field($model, 'unit')->textInput(['class' => 'text-right form-control', 'id' => 'unit-line'])->label(false);?></dd>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <dt></dt>
                <dd><?= $form->field($model, 'royaltyFree')->checkbox();?></dd>
            </div>
        </div>
        <?php endif;?>
    </dl>
    <?php ActiveForm::end(); ?>
</div>