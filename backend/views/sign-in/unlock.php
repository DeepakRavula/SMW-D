<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\LoginForm */

$this->title = Yii::t('backend', 'Sign In');
$this->params['breadcrumbs'][] = $this->title;
$this->params['body-class'] = 'login-page';
?>
<div class="login-box">
    
    <div class="login-logo">
        <a href="<?php echo Yii::getAlias('@frontendUrl') ?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
               <img class="login-logo-img" src="<?= env('SITE_URL') ?>/img/logo.png"  />      
            </a>
        <?php //echo Html::encode($this->title)?>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <div class="body">
			<div id="admin-login">
				Office Login
			</div>
			<div id="admin-login-form">
            <?php echo $form->field($model, 'pin')->label('Pin') ?>
           
			</div>	
        </div>
        <div class="footer">
            <?php echo Html::submitButton(Yii::t('backend', 'Sign me in'), [
                'class' => 'btn btn-primary btn-flat btn-block',
                'name' => 'login-button',
            ]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>