<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Province;

/* @var $this yii\web\View */
/* @var $model common\models\City */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="city-form">

    <?php $form = ActiveForm::begin(); ?>

  	<div class="col-md-12">
		<?= $form->errorSummary($model); ?>
	</div>
	<div class="row">
		<div class="col-md-4">
    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4 ">
    <?php echo $form->field($model, 'province_id')->dropDownList(ArrayHelper::map(
							Province::find()->all(), 'id', 'name'
			)) ?>
		</div>
</div>


    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		<?php 
			if(! $model->isNewRecord){
				echo Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn btn-primary']); 	
			}
		?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
