<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\LoginForm */

?>
<div class="login-box">
    
    <div class="login-box-body">
        <?php $form = ActiveForm::begin(['id' => 'user-pin-form']); ?>
        <div class="body">
            <div id="admin-login-form">
                <?php echo $form->field($model, 'pin')->label('Pin') ?>
            </div>	
        </div>
        <div class="footer">
            <?php echo Html::submitButton(Yii::t('backend', 'Update'), [
                'class' => 'btn btn-primary btn-flat btn-block',
                'name' => 'login-button',
            ]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>