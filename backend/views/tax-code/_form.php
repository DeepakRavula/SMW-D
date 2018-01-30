<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\TaxType;
use common\models\Province;

/* @var $this yii\web\View */
/* @var $model common\models\TaxCode */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="tax-code-form">

    <?php $form = ActiveForm::begin([
        'id' => 'taxcode-form',
    ]); ?>

    <?php echo $form->errorSummary($model); ?>
	<div class="row">
        <div class="col-xs-4">
		<?php
            echo $form->field($model, 'tax_type_id')->dropDownList(ArrayHelper::map(
                            TaxType::find()->all(),
        'id',
        'name'
            ))
            ?>
        </div>
        <div class="col-xs-4">
             <?php echo $form->field($model, 'province_id')->dropDownList(ArrayHelper::map(
                            Province::find()->orderBy(['name' => SORT_ASC])->all(),
                'id',
                'name'
            ))
            ?> 
        </div>
		<div class="col-xs-4">
             <?php echo $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
             <?php echo $form->field($model, 'rate')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-xs-4">
            <?php echo $form->field($model, 'start_date')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class' => 'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '-70:today',
                    ],
                ]); ?>
        </div>
    </div>
<div class="row">
<div class="col-md-12">    
    <div class="pull-right">
        <?php echo Html::a('Cancel', '#', ['class' => 'btn btn-default taxcode-cancel']);?>
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
    </div>
        <div class="pull-left">
        <?php if (!$model->isNewRecord) {
                    echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'id' => 'taxcode-delete-button',
                'class' => 'btn btn-danger',
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
</div>

    <?php ActiveForm::end(); ?>

</div>
