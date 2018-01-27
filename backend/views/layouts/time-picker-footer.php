<?php
use yii\helpers\Html;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="col-lg-12">
    <div class="form-group pull-right m-t-10">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default calendar-date-time-picker-cancel']); ?>
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info calendar-date-time-picker-save', 'name' => 'button']) ?>
    </div>
</div>