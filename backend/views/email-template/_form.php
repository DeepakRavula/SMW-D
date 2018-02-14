<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\ArrayHelper;
use common\models\EmailObject;
/* @var $this yii\web\View */
/* @var $model common\models\EmailTemplate */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="mail-form">
<?php 
        $form = ActiveForm::begin([
        'id' => 'email-template-form',
    ]); ?>
    	<?= $form->field($model, 'subject')->textInput()?>
    	<?= $form->field($model, 'header')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic',
    ]) ?>
    	<?= $form->field($model, 'footer')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic',
    ]) ?>
	<div class="clearfix"></div>
    <div class="row-fluid">
    <div class="form-group pull-right">
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default template-cancel']);?>
        <?php echo Html::submitButton('Update', ['class' => 'btn btn-info']) ?>
    </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>