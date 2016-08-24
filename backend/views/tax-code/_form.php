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

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>
	<div class="row">
        <div class="col-xs-4">
		<?php
			echo $form->field($model, 'tax_type_id')->dropDownList(ArrayHelper::map(
							TaxType::find()->all(), 'id', 'name'
			))
			?>
        </div>
        <div class="col-xs-4">
             <?php echo $form->field($model, 'province_id')->dropDownList(ArrayHelper::map(
							Province::find()->all(), 'id', 'name'
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
                    'options' => ['class'=>'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '-70:today' 
                    ]
                ]); ?>
        </div>
    </div>
    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
