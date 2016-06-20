<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use common\models\Province;

/* @var $this yii\web\View */
/* @var $model common\models\Tax */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="tax-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?> 
  
    <?php echo $form->field($model, 'province_id')->dropDownList(\yii\helpers\ArrayHelper::map(
            Province::find()->all(),
            'id',
            'name'
        ), ['prompt'=>'']) ?>

    <?php echo $form->field($model, 'tax_rate')->textInput() ?>

    <?php echo $form->field($model, 'since')->widget(\yii\jui\DatePicker::classname(), [
                'options' => ['class'=>'form-control'],
				'clientOptions' => [
					'changeMonth' => true,
					'changeYear' => true,
					'yearRange' => '-2:+70'	
				]
    ]); ?>


    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
