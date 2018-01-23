<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-form">
<?php $form = ActiveForm::begin([
    'id' => 'lesson-note-form',
]); ?>
<div class="row p-20">
	<div>
        <?php echo $form->field($model, 'content')->textarea(['rows' => '10'])->label(false)?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
    </div>
</div>
    </div>
<?php ActiveForm::end(); ?>
</div>