<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\PasswordResetRequestForm */

$this->title = Yii::t('frontend', 'Request password reset');
$this->params['breadcrumbs'][] = $this->title;
?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<div class="login-box">
	 <div class="login-logo">
        <a href="<?php echo Yii::getAlias('@frontendUrl') ?>" class="logo">  
            <!-- Add the class icon to your logo image or logo icon to add the margining -->            
            <img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
        </a>
    </div><!-- /.login-logo -->
    <div class="header"></div>
<?php if (!$isEmailSent): ?>
	    <div class="site-request-password-reset login-box-body">
	        <h4><?php echo Html::encode($this->title) ?></h4>
	        <p>To reset your password, please enter an email associated with your account.</p>

	        <div class="body">
				<?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
					<?php echo $form->field($model, 'email') ?>
					<div style="background-color: #f8f8f8;">
                        <div class="g-recaptcha captcha-style" data-sitekey="6Le5FPoZAAAAAKH2yl9BjAe8dOMGKE2allr9Ra_a"></div>
                        <?php 
                                        if (\Yii::$app->session->get('captcha-error')) {
                                    ?>
                                    <p class="captcha-error" style="color:red;"><?php echo \Yii::$app->session->get('captcha-error'); ?></p>
                
                                    <?php } ?>
                    </div>
				<div class="form-group">
					<?php
                    echo Html::submitButton('Send', [
                        'class' => 'btn btn-primary btn-flat btn-block',
                        'name' => 'login-button',
                    ])
                    ?>
				</div>
				<div class="m-t-10 text-left">
				<?php
                echo Yii::t('frontend', '<a href="{link}"><i class="fa fa-angle-left"></i> Back</a>', [
                    'link' => yii\helpers\Url::to(['sign-in/login']),
                ])
                ?>
				</div>
			<?php ActiveForm::end(); ?>
	        </div>
	    </div>
	<?php else: ?>
	    <div class="site-request-password-reset login-box-body">
	        <p>A password reset link has been sent to your email.</p>
	<?php echo Html::a('Login', ['sign-in/login'], ['class' => 'btn btn-primary btn-flat btn-block']) ?>
	    </div>
<?php endif; ?>
</div>
