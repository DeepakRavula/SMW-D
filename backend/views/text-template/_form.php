<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Holiday */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="holiday-form">
<?php 
        $form = ActiveForm::begin([
        'id' => 'template-form',
        'action' => Url::to(['text-template/update', 'id' => $model->id]),
    ]); ?>
	<?php echo $form->field($model, 'message')->textarea(['rows' => 6]); ?>
	<div class="clearfix"></div>
    <div class="row-fluid">
    <div class="form-group pull-right">
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default template-cancel']);?>
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
       
    </div>
    <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
