<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model common\models\ReminderNote */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="reminder-notes-form p-10">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?= $form->field($model, 'notes')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'full',
    ]) ?>
<div class="row">
    <div class="col-md-12">
    <div class="form-group pull-right">
        <?php if (!$model->isNewRecord) : ?>
         <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default']);?>
		<?php endif; ?>
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info']) ?>
    </div> 
    </div>
</div>
    <?php ActiveForm::end(); ?>

</div>
