<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\switchinput\SwitchInput;
use yii\bootstrap\ActiveForm;
use common\models\Item;
use common\models\ItemCategory;
use common\models\TaxStatus;
use yii\helpers\Url;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Item */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="item-form">

    <?php   $url = Url::to(['item/update', 'id' => $model->id]);
            if ($model->isNewRecord) {
               $url = Url::to(['item/create']);
            }
        $form = ActiveForm::begin([
        'id' => 'update-item-form',
        'action' => $url,
    ]); ?>
<div class="row">
    <div class="col-xs-6">
        <?php echo $form->field($model, 'itemCategoryId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(ItemCategory::find()
                    ->notDeleted()
                    ->active()
                   ->orderBy(['name' => SORT_ASC])
                    ->all(), 'id', 'name'),
                'options' => ['placeholder' => 'Select Category'],
            ]);
        ?>
    </div>
    <div class="col-xs-6">
        <?php echo $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-12">
        <?php echo $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-xs-6">
        <?php echo $form->field($model, 'price')->textInput() ?>
    </div>
    <div class="col-xs-6">
        <?= $form->field($model, 'royaltyFree')->widget(SwitchInput::classname(),
                    [
                    'name' => 'royaltyFree',
                    'pluginOptions' => [
                        'handleWidth' => 30,
                        'onText' => 'Yes',
                        'offText' => 'No',
                    ],
                ]);?>
    </div>
    <div class="col-xs-6">
        <?php echo $form->field($model, 'taxStatusId')->dropDownList
            (ArrayHelper::map(TaxStatus::find()->all(), 'id', 'name'), ['prompt' => 'Select Tax']) ?>
    </div>
    <div class="col-xs-6">
        <?php echo $form->field($model, 'status')->dropDownList
            (Item::itemStatuses()) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default item-cancel']);?>
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info']) ?>
        </div> 
        <div class="pull-left">
 <?php if (!$model->isNewRecord) {
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
			'id' => 'item-delete-button',
                        'class' => 'btn btn-primary',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ]
                ]);
            }
        ?>
    </div>
    </div>
</div>
    <?php ActiveForm::end(); ?>

</div>
