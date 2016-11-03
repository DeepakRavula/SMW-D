<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
    <div class="col-md-8">
   <?php 
   echo '<label>Date Range</label>';
   echo DateRangePicker::widget([
    'model' => $model,
    'attribute' => 'dateRange',
    'convertFormat' => true,
    'initRangeExpr' => true,
    'pluginOptions' => [
        'autoApply' => true,
        'ranges' => [
            Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')", 'moment()'],
            Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')", 'moment()'],
            Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')", "moment().endOf('month')"],
            Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
        ],
        'locale' => [
            'format' => 'd-m-Y',
        ],
        'opens' => 'left',
        ],

    ]);
   ?>
   </div>    
    <div class="col-md-4 form-group m-t-20">
        <?php echo Html::submitButton(Yii::t('backend', 'Apply'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
        <div class="clearfix"></div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
