<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\ResetPasswordForm */

$this->title = Yii::t('frontend', 'Reset password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-box">
    
    <div class="login-logo">
        <a href="<?php echo Yii::getAlias('@frontendUrl') ?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
            </a>
        <?php //echo Html::encode($this->title) ?>
    </div><!-- /.login-logo -->
    <div class="header"></div>
    <div class="site-reset-password login-box-body">
        <h1><?php echo Html::encode($this->title) ?></h1>

        <div class="body">
                <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                    <?php echo $form->field($model, 'password')->passwordInput() ?>
                    <div class="form-group">
                        <?php echo Html::submitButton('Save', [
                            'class' => 'btn btn-primary btn-flat btn-block',
                            'name' => 'login-button'
                        ]) ?>
                    </div>
                <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>w
