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
	</div>
    <div class="header"></div>

<?php if ($tokenExpired): ?>
		<div class="site-request-password-reset login-box-body">
	        <p>Your password reset link has been expired. Please try reset password flow again. </p>
	    </div>
	<?php else: ?>	
	<?php if (!$isResetPassword): ?>
		    <div class="site-reset-password login-box-body">
		        <h4><?php echo Html::encode($this->title) ?></h4>

		        <div class="body">
					<?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
					<?php echo $form->field($model, 'password')->passwordInput() ?>
						<?php echo $form->field($model, 'reenterpassword')->passwordInput() ?>
					<div class="form-group">
						<?php
                        echo Html::submitButton('Save', [
                            'class' => 'btn btn-info btn-flat btn-block',
                            'name' => 'login-button',
                        ])
                        ?>
					</div>
			<?php ActiveForm::end(); ?>
		        </div>
		    </div>
	<?php else: ?>
		    <div class="site-request-password-reset login-box-body">
		        <p>Password has been changed successfully.</p>
		        <?php echo Html::a('Login', ['/sign-in/login'], ['class' => 'btn btn-primary btn-flat btn-block']) ?>
		    </div>
	<?php endif; ?>
<?php endif; ?>
</div>
