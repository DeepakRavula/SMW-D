<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\PasswordResetRequestForm */

$this->title =  Yii::t('frontend', 'Request password reset');
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
    <div class="site-request-password-reset">
        <h1><?php echo Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-lg-5">
                <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
                    <?php echo $form->field($model, 'email') ?>
                    <div class="form-group">
                        <?php echo Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
