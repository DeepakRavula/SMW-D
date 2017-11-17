<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\switchinput\SwitchInput;
use yii\helpers\ArrayHelper;
use common\models\User;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div id="warning-notification" style="display:none;" class="alert-warning alert fade in"></div>
<div id="enrolment-enddate" style="display:none;" class="alert-danger alert fade in"></div>
    <?php $form = ActiveForm::begin([
        'id' => 'enrolment-rate-form',
        'action' => Url::to(['enrolment/edit', 'id' => $model->id]),
    ]); ?>
<div class="row">
    <?php
        $roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id), 'name');
        $role = end($roles);
    ?>
    
    <?php if ($role === User::ROLE_ADMINISTRATOR) : ?>
        <div class="col-md-6">
            <label>Rate From <?= $enrolmentProgramRate->startDate . ' To ' . $enrolmentProgramRate->endDate ?></label>
            <?= $form->field($enrolmentProgramRate, 'programRate')->textInput()->label(false); ?>
        </div>
    <?php else : ?>
        <div class="col-md-6">
            <?= $form->field($enrolmentProgramRate, 'programRate')->hiddenInput()->label(false); ?>
        </div>
    <?php endif; ?>   
    
    <div class="col-md-4">
        <?php echo $form->field($model, 'isAutoRenew')->widget(SwitchInput::classname(),
                [
                'pluginOptions' => [
                    'size' => 'Medium',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                    'handleWidth' => 60,
                    'onText' => 'Enable',
                    'offText' => 'Disable',
                ],
        ])->label('Auto Renew');?>
    </div>
		<div class="clearfix"></div>
		 <div id="spinner" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>
            <div class="form-group col-md-12">
<div class="pull-right">
            <?= Html::a('Cancel', '', ['class' => 'btn btn-default enrolment-rate-cancel']);?>
            <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button', 'id' => 'enrolment-edit-save-btn']) ?>
</div>        
</div>
</div>
    <?php ActiveForm::end(); ?>