<?php

use common\models\Program;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="program-form">

    <?php $form = ActiveForm::begin(); ?>

   	<div class="row">
		<div class="col-md-4">
			<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'rate')->textInput() ?>
		</div>
		<div class="col-md-4">
			<?php if (!$model->getIsNewRecord()) : ?>
			<?php echo $form->field($model, 'status')->dropDownList(Program::statuses()) ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="row-fluid">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php 
			if(! $model->isNewRecord){
				echo Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	
			}
		?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
