<?php

use common\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use trntv\filekit\widget\Upload;

$loggedUser = User::findOne(Yii::$app->user->id);
?>
<?php
    $form = ActiveForm::begin([
        'id' => 'user-update-form',
        'action' => Url::to(['user/edit-profile', 'id' => $model->getModel()->id])
    ]);
    ?>
    <div class="row">
	<div class="col-xs-6">
            <?php echo $form->field($model, 'firstname') ?>
	</div>
	<div class="col-xs-6">
            <?php echo $form->field($model, 'lastname') ?>
	</div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <?php echo $form->field($model, 'status')->dropDownList(User::status()) ?>
        </div>
        <?php if ($loggedUser->canManagePin()) : ?>
            <?php if ($model->getModel()->hasPin()) : ?>
                <div class="col-xs-6">
                    <?php echo $form->field($model, 'pin')->passwordInput() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="col-xs-6 can-login" style="display: none">
                <?php echo $form->field($model, 'password')->passwordInput() ?>
        </div>
        <div class="col-xs-6 can-login" style="display: none">
                <?php echo $form->field($model, 'confirmPassword')->passwordInput() ?>
        </div>
    </div>
    <div class="row">
        <?php if ($loggedUser->canManagePin()) : ?>
        <?php if ($model->getModel()->isStaff()) : ?>
            <div class="col-xs-6">
                <?php echo $form->field($model, 'canLogin')->checkbox() ?>
            </div>
        <?php endif; ?>
        <?php endif; ?>
        <div class="col-xs-6">
        <?= $form->field($userProfile, 'picture')->widget(
            Upload::classname(),
            [
            'url' => ['avatar-upload']
        ]
        )
        ?>
        </div>
    </div>
    <div class="row">
	<div class="col-md-12">
            <div class="pull-right">
                <?php echo Html::a('Cancel', '#', [ 'id' => 'user-cancel-btn', 'class' => 'btn btn-default']);?>
                <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function() {
        var canLogin = <?= $model->canLogin ?>;
        if (canLogin) {
            $('.can-login').show();
        } else {
            $('.can-login').hide();
        }
    });
    
    $(document).on('change', '#userform-canlogin', function () {
        userProfile.managePasswordField();
    });
    
    var userProfile = {
        managePasswordField :function() {
            if ($('#userform-canlogin').is(':checked')) {
                $('.can-login').show();
            } else {
                $('.can-login').hide();
            }
        }
    }
</script>
