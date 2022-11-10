<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-form">
<?php $form = ActiveForm::begin([
    'id' => 'invoice-message-form',
    'action' => Url::to(['proforma-invoice/note', 'id' => $model->id])
]); ?>
<div class="row">
	<div class="col-md-12">
    <?php echo $form->field($model, 'notes')->textarea(['rows' => 6])->label(false)?>
    </div>
    <div class="form-group pull-right">
		<?php echo Html::a('Cancel', '', ['class' => 'm-r-10 btn btn-default invoice-note-cancel']) ?>
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['id' => 'invoice-message-save', 'class' => 'm-r-10 btn btn-info', 'name' => 'invoice-message-button']) ?>
		
    </div>
</div>
<?php ActiveForm::end(); ?>
</div>