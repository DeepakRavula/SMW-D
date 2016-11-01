<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Holiday */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="holiday-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-xs-4">
            <?php echo $form->field($model, 'date')->widget(\yii\jui\DatePicker::classname(), [
                    'options' => ['class' => 'form-control'],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'changeYear' => true,
                        'yearRange' => '-70:today',
                    ],
                ]); ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row-fluid">
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php 
            if (!$model->isNewRecord) {
                echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
            }
        ?>
    </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
