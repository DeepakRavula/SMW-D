<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\ArrayHelper;
use common\models\Blog;
/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="blog-form">
<?php 
        $form = ActiveForm::begin([
        'id' => 'blog-form',
    ]); ?>
    	<?= $form->field($model, 'title')->textInput()?>
    	<?= $form->field($model, 'content')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic',
    ]) ?>
    	
	<div class="clearfix"></div>
    <div class="row-fluid">
    <div class="form-group pull-right">
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default blog-cancel']);?>
       <?php echo Html::submitButton($model->isNewRecord ? 'Save' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-info' : 'btn btn-info']) ?>
    </div>
    <div class="pull-left">   
        <?php if (!$model->isNewRecord) {
                echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'id' => 'blog-delete-button',
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
</div>
    <?php ActiveForm::end(); ?>