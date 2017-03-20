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

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		<?php if (!$model->isNewRecord) : ?>
         <?= Html::a('Cancel', ['index'], ['class' => 'btn']);?>
		<?php endif; ?> 
    </div>

    <?php ActiveForm::end(); ?>

</div>
