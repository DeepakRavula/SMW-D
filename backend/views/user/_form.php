<?php

use yii\bootstrap\ActiveForm;
use kartik\select2\Select2Asset;
use yii\helpers\Url;
use common\models\User;
use common\models\ReferralSources;
use yii\helpers\ArrayHelper;
Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<div class="row user-create-form">
    <?php $form = ActiveForm::begin([
        'action' => Url::to(['user/create', 'role_name' => $model->roles]),
        'id' => 'modal-form',
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['user-contact/validate']),
    ]); ?>
    <div class="row">
        <?php echo $form->field($model, 'firstname') ?>
        <?php echo $form->field($model, 'lastname') ?>		
        <?= $form->field($emailModel, 'email')->textInput(['maxlength' => true])->label('Email (Work)') ?>
        <div id = "referal-source">
            <?php 
            $referralSources = ReferralSources::find()
            ->notDeleted()
            ->all();
            $referralSourcesList = ArrayHelper::map($referralSources, 'id', 'name');
                if($model->roles == User::ROLE_CUSTOMER) {
            echo  $form->field($customerReferralSources, 'referralSourceId')->radioList($referralSourcesList)->label('How did you find us?');
                }
            ?>
        </div>
    </div>
    
<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).off('click', 'input:radio[name="CustomerReferralSources[referralSourceId]"]').on('click', 'input:radio[name="CustomerReferralSources[referralSourceId]"]', function () {
        var referralSourceId = $('input:radio[name="CustomerReferralSources[referralSourceId]"]:checked').val();
        if(referralSourceId == '4') {
            $('#customerreferralsources-referralsourceid').hide();
            $('#referal-source').append(
            '<input class="form-control" id="customer-referral-source-description" name="CustomerReferralSources[description]" type="input" placeholder=""name="Description"/>');
        }
    });

</script>
