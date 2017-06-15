<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\ResetPasswordForm */

$this->title = Yii::t('frontend', 'Reset password');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if ($tokenExpired): ?>
		<div class="site-request-password-reset login-box-body">
	        <p>Your password reset link has been expired. Please try reset password flow again. </p>
	    </div>
<?php else: ?>	
	<?php if (!$isResetPassword): ?>
<div class="site-reset-password">
    <h1><?php echo Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                <?php echo $form->field($model, 'password')->passwordInput() ?>
				<?php echo $form->field($model, 'confirmPassword')->passwordInput() ?>
                <div class="form-group">
                    <?php echo Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php else: ?>
		    <div class="site-request-password-reset login-box-body">
		        <p>Password has been changed successfully.</p>
		        <?php echo Html::a('Login', ['/user/sign-in/login'], ['class' => 'btn btn-primary btn-flat btn-block']) ?>
		    </div>
	<?php endif; ?>
<?php endif; ?>
