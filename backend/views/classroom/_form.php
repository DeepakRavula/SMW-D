<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Classroom */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="class-room-form">

    <?php $form = ActiveForm::begin(); ?>

	<div class="row p-10">
	<div class="col-md-4">
    	<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	</div>
	</div>
    <div class="form-group p-10">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		<?php if (!$model->isNewRecord) : ?>
         <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);?>
		<?php endif; ?>            
    </div>

    <?php ActiveForm::end(); ?>

</div>
