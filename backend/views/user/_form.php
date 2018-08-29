<?php

use yii\bootstrap\ActiveForm;
use kartik\select2\Select2Asset;
use yii\helpers\Url;
use common\models\User;
use common\models\ReferralSource;
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
            $referralSource = ReferralSource::find()
            ->notDeleted()
            ->all();
            $referralSourceList = ArrayHelper::map($referralSource, 'id', 'name');
                if ($model->roles == User::ROLE_CUSTOMER) {
                    echo  $form->field($customerReferralSource, 'referralSourceId')->radioList($referralSourceList)->label('How did you find us?');
                    echo  $form->field($customerReferralSource, 'description')->textInput()->label(false);
                }
            ?>
        </div>
    </div>
    
<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function(){
        $("#customerreferralsource-description").hide();
    });
    
    $(document).off('click', 'input:radio[name="CustomerReferralSource[referralSourceId]"]').on('click', 'input:radio[name="CustomerReferralSource[referralSourceId]"]', function () {
        var referralSourceId = $('input:radio[name="CustomerReferralSource[referralSourceId]"]:checked').val();
        if (referralSourceId == '4') {
            $("#customerreferralsource-description").show();
        } else {
            $("#customerreferralsource-description").hide();
        }
    });

</script>
