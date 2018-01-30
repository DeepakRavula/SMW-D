<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\ArrayHelper;
use common\models\EmailType;
/* @var $this yii\web\View */
/* @var $model common\models\EmailTemplate */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="mail-form">
<?php 
        $form = ActiveForm::begin([
        'id' => 'email-template-form',
    ]); ?>
    <?php echo $form->field($model, 'emailTypeId')->dropDownList(ArrayHelper::map(
                            EmailType::find()->orderBy(['name' => SORT_ASC])->all(),
        'id',
        'name'
            )) ?>
    	<?= $form->field($model, 'subject')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic',
    ]) ?>
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
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-info']) ?>
    </div>
    <div class="pull-left">   
        <?php if (!$model->isNewRecord) {
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'id' => 'email-delete-button',
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ]
                ]);
            }
        ?>
    </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>