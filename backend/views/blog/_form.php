<?php

use yii\helpers\Html;
use dosamigos\ckeditor\CKEditor;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="blog-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'title')->textInput() ?>
	
	<?= $form->field($model, 'content')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'full',
    ]) ?>
<div class="row">
    <div class="col-md-12">
    <div class="form-group pull-right">
        <?php if (!$model->isNewRecord) : ?>
         <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-default']);?>
		<?php endif; ?> 
        <?php echo Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-info']) ?>
		
    </div>
    </div>
</div>
    <?php ActiveForm::end(); ?>

</div>
