<?php

use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;
/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="student-form">
    <?php $model->content = $content;
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['email/send'])
    ]);
    ?>
    <?php if (!empty($invoiceId)) : ?>
    <?= $form->field($model, 'invoiceId')->hiddenInput(['value' => $invoiceId])->label(false) ?>
    <?php endif; ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'to')->widget(Select2::classname(), [
                'data' => $data,
                'pluginOptions' => [
                    'tags' => true,
                    'multiple' => true,
                ],
            ]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'subject')->textInput(['value' => $subject]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <?= $form->field($model, 'content')->widget(CKEditor::className()); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
	$(document).ready(function(){
		$('#popup-modal .modal-dialog').css({'width': '1000px'});
                        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Email Preview</h4>');
                        $('.modal-save').text('Send');
                        $('.modal-back').hide();
                        $('.modal-save-all').hide();
	});
	</script>