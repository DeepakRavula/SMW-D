<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\switchinput\SwitchInput;
use yii\bootstrap\ActiveForm;
use common\models\Item;
use common\models\ItemCategory;
use common\models\TaxStatus;

/* @var $this yii\web\View */
/* @var $model common\models\Item */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'itemCategoryId')->dropDownList
        (ArrayHelper::map(ItemCategory::find()->all(), 'id', 'name'), ['prompt' => 'Select Category']) ?>

    <?php echo $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'royaltyFree')->widget(SwitchInput::classname(),
                [
                'name' => 'isRoyalty',
                'pluginOptions' => [
                    'handleWidth' => 30,
                    'onText' => 'Yes',
                    'offText' => 'No',
                ],
            ]);?>

    <?php echo $form->field($model, 'taxStatusId')->dropDownList
        (ArrayHelper::map(TaxStatus::find()->all(), 'id', 'name'), ['prompt' => 'Select Tax']) ?>

    <?php echo $form->field($model, 'status')->dropDownList
        (Item::itemStatuses()) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
