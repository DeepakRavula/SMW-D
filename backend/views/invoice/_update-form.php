<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="invoice-form p-10">

    <?php $form = ActiveForm::begin(); ?>

   	<div class="row">
	<div class="col-md-4">
		<?php echo $form->field($model, 'notes')->textarea() ?>
	</div>
    <div class="clearfix"></div>
    </div>
    <div class="form-group">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
		<?php 
            if (!$model->isNewRecord) {
                echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-default']);
            }
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
