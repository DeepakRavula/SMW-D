<?php

use yii\helpers\Html; 
use yii\bootstrap\ActiveForm; 
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */ 
/* @var $model common\models\ReleaseNotes */ 
/* @var $form yii\bootstrap\ActiveForm */ 
?> 

<div class="release-notes-form"> 

    <?php $form = ActiveForm::begin(); ?> 

    <?php echo $form->errorSummary($model); ?> 
    
    <?= $form->field($model, 'notes')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'full'
    ]) ?>

    <div class="form-group"> 
        <?php echo Html::submitButton($model->isNewRecord ? 'Save & Publish' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?> 
    </div> 

    <?php ActiveForm::end(); ?> 

</div> 
 