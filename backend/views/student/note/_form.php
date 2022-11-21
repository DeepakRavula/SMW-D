<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-form">
<?php $form = ActiveForm::begin([
    'id' => 'student-note-form',
]); ?>
<div class="">
	<div>
        <?php echo $form->field($model, 'content')->textarea(['rows' => '10'])->label(false)?>
    </div>
    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default note-cancel-button']); ?>
    </div>
</div>
<div class="clearfix"></div>
<?php ActiveForm::end(); ?>
</div>