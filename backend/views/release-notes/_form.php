<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model common\models\ReleaseNotes */
/* @var $form yii\bootstrap\ActiveForm */
?> 

<div class="release-notes-form"> 

    <?php
$form = ActiveForm::begin(['id' => 'new-release-notes-form']);
?> 

    <?php echo $form->errorSummary($model); ?> 
    
    <?= $form->field($model, 'notes')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'full',
    ]) ?>
<div class="row">
    <div class="col-md-12">
         <div class="pull-right"> 
        <?php echo Html::submitButton($model->isNewRecord ? 'Save & Publish' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary btn-sm' : 'btn btn-info btn-sm']) ?> 
    </div> 
  </div>
</div>
    <?php ActiveForm::end(); ?> 

</div> 
 