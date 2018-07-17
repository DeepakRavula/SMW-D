<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;
/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="blog-form">
<?php 
        $url = Url::to(['blog/update', 'id' => $model->id]);
    if ($model->isNewRecord) {
        $url = Url::to(['blog/create']);
    }
        $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
    ]); ?>
    	<?= $form->field($model, 'title')->textInput()?>
    	<?= $form->field($model, 'content')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'basic',
    ]) ?>
</div>
    <?php ActiveForm::end(); ?>
<script>
    $(document).on('modal-success', function(event, params) {
        var url = "<?php echo Url::to(['blog/index']); ?>";
        $.pjax.reload({url: url, container: "#blog-listing", replace: false, timeout: 4000});
        return false;
    });
    
    $(document).on('modal-delete', function(event, params) {
        var url = "<?php echo Url::to(['blog/index']); ?>";
        $.pjax.reload({url: url, container: "#blog-listing", replace: false, timeout: 4000});
        return false;
    });
</script>
