<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
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
    <?php $user = User::findOne(Yii::$app->user->id); ?>
    <?php foreach ($enrolmentProgramRates as $key => $enrolmentProgramRate) : ?>
    <?php if ($user->isAdmin()) : ?>
        <div class="col-md-6">
            <label>Rate From <?= (new \DateTime($enrolmentProgramRate->startDate))->format('d-m-Y') 
                . ' To ' . (new \DateTime($enrolmentProgramRate->endDate))->format('d-m-Y') ?></label>
            <?= $form->field($enrolmentProgramRate, 'programRate')->textInput([
                    'id' => 'program-rate' . $key, 'name' => 'EnrolmentProgramRate['. $key . '][programRate]'
                ])->label(false); 
            ?>
        </div>
    <?php else : ?>
        <div class="col-md-6">
            <?= $form->field($enrolmentProgramRate, 'programRate')->hiddenInput([
                'id' => 'program-rate' . $key, 'name' => 'EnrolmentProgramRate['. $key . '][programRate]'
                ])->label(false); 
            ?>
        </div>
    <?php endif; ?>   
    <?php endforeach; ?>   
    <div class="col-md-12">
        <label>Auto Renew</label>
        <?= $form->field($model, 'isAutoRenew')->checkbox(['data-toggle' => "toggle", 
            "data-on" => "Enable", "data-off" => "Disable", 'data-width' => '100',
            "data-onstyle" => "success", "data-offstyle" => "danger"])->label(false); 
        ?>
    </div>
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