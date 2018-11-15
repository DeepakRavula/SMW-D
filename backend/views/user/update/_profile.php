<?php

use common\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use trntv\filekit\widget\Upload;
use yii\jui\DatePicker;
use common\models\ReferralSource;
use yii\helpers\ArrayHelper;
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
            <?php echo $form->field($userProfile, 'firstname') ?>
	</div>
	<div class="col-xs-6">
            <?php echo $form->field($userProfile, 'lastname') ?>
	</div>
    </div>
        <div class="row">
        <div class="col-xs-6 can-login" style="display: none">
                <?php echo $form->field($model, 'password')->passwordInput() ?>
        </div>
        <div class="col-xs-6 can-login" style="display: none">
                <?php echo $form->field($model, 'confirmPassword')->passwordInput() ?>
        </div>
    </div>
    <div class="row">
		<?php if ($model->getModel()->isTeacher()) : ?>
	        <div class="col-xs-4">
            <?php if (!($userProfile->isNewRecord) &&  ($userProfile->birthDate)) : ?>
                <?php $userProfile->birthDate = (new \DateTime($userProfile->birthDate))->format('M d, Y'); ?>
            <?php endif; ?>
				<?php
				echo $form->field($userProfile, 'birthDate')->widget(DatePicker::classname(),
					[
						'dateFormat' => 'php:M d, Y',
            'clientOptions' => [
                'defaultDate' => (new \DateTime($userProfile->birthDate))->format('M d, Y'),
                'changeMonth' => true,
                'yearRange' => '-70:+0',
                'changeYear' => true,
                ],
				])->textInput(['placeholder' => 'Select Date', 'readOnly' => true])->label('Birth Date');
				?>
			</div>
		<?php endif; ?>
        <?php if ($model->getModel()->isCustomer()) : ?>
            <div class = "col-xs-12">    
                <div class="col-xs-6">
                    <label class="modal-form-label">How did you find us?</label>
                </div>    
                <div class="col-xs-4">
                    <div class = "referral-source">
                        <?php 
                        $referralSource = ReferralSource::find()
                        ->notDeleted()
                        ->all();
                        $referralSourceList = ArrayHelper::map($referralSource, 'id', 'name'); ?>
                           <?php if ($model->roles == User::ROLE_CUSTOMER) :?>
                               <?php echo  $form->field($customerReferralSource, 'referralSourceId')->radioList($referralSourceList)->label(false); ?>
                               <?php echo  $form->field($customerReferralSource, 'description')->textInput()->label(false); ?>
                            <?php endif; ?>  
                    </div>
                </div> 
            </div>
		<?php endif; ?>
        <div class="col-xs-4">
        <?php if ($loggedUser->canManagePin()) : ?>
            <?php if ($model->getModel()->hasPin()) : ?>
                    <?php echo $form->field($model, 'pin')->passwordInput() ?>
            <?php endif; ?>          
        <?php endif; ?>
            </div>
    </div>
    <?php if ($loggedUser->isAdmin()) : ?>
        <?php if ($model->getModel()->isOwner()) : ?>
            <div class="row">
                <div class="col-xs-6">
                    <?= $form->field($model, 'canMerge')->checkbox() ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
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
        $("#customerreferralsource-description").hide();
		var canLogin = <?= $model->canLogin ?>;
        if (canLogin) {
            $('.can-login').show();
        } else {
            $('.can-login').hide();
        }
        var referralSource = '<?= $model->getModel()->customerReferralSource ? $model->getModel()->customerReferralSource->referralSourceId : null  ?>';
        if (!$.isEmptyObject(referralSource) && referralSource == 4 ) {
            $("#customerreferralsource-description").show();
        }
    });
    
    $(document).on('change', '#userform-canlogin', function () {
        userProfile.managePasswordField();
    });
    
    $(document).off('click', 'input:radio[name="CustomerReferralSource[referralSourceId]"]').on('click', 'input:radio[name="CustomerReferralSource[referralSourceId]"]', function () {
        var referralSourceId = $('input:radio[name="CustomerReferralSource[referralSourceId]"]:checked').val();
        if (referralSourceId == '4') {
            $("#customerreferralsource-description").show();
        } else {
            $("#customerreferralsource-description").hide();
        }
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
