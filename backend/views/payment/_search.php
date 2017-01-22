<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
/*  .e1Div{
    right: 0 !important;
    top: -59px;
  }*/
    .e1Div{
    position: absolute;
    right: 155px;
    top: 17px;
    line-height: 37px;
    }
</style>
<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
    <div class="col-md-3">
        <?php echo $form->field($model, 'fromDate')->widget(DatePicker::classname(), [
            'options' => [
                'id' => 'from-date',
                'class' => 'form-control',
            ],
        ]) ?>
    </div>
    <div class="col-md-3">
        <?php echo $form->field($model, 'toDate')->widget(DatePicker::classname(), [
            'options' => [
                'id' => 'to-date',
                'class' => 'form-control',
            ],
        ]) ?>
    </div>
    <div class="pull-right  m-r-20">
        <div class="schedule-index">
            <div class="e1Div">
                <?= $form->field($model, 'groupByMethod')->checkbox(['id' => 'group-by-method', 'data-pjax' => true]); ?>
            </div>
        </div>
    </div>
    <div class="col-md-3 form-group m-t-20">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
        <div class="clearfix"></div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
