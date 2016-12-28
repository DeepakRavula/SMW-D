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
<div class="row p-20">
	<div>
        <?php echo $form->field($model, 'content')->textarea(['rows' => '10'])->label(false)?>
    </div>
    <div class="col-md-12 p-l-20 form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php if(! $model->isNewRecord) : ?>
            <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']); ?>
		<?php endif; ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
</div>