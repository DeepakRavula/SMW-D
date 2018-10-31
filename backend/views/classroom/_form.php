<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Classroom */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class=" row user-create-form">

    <?php 
    $url = Url::to(['classroom/update', 'id' => $model->id]);
    if ($model->isNewRecord) {
        $url = Url::to(['classroom/create']);
    }
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
    ]); ?>

	<div class="row">
    	<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    	<?php echo $form->field($model, 'description')->textArea(['maxlength' => true]) ?>
	</div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Classroom</h4>');
        $('#popup-modal .modal-dialog').addClass('classroom-dialog'); 
    });
</script>