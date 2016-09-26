<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\search\DashboardSearch;
use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;;

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
    <div class="col-md-6">
   <?php 
   echo '<label>Date Range</label>';
   echo DateRangePicker::widget([
    'model'=>$model,
    'attribute'=>'dateRange',
    'convertFormat'=>true,    
    'pluginOptions'=>[ 
            'locale'=>[        
            'format'=>'d-m-Y'
        ]
    ]
    ]);
   ?>
   </div>    
    <div class="col-md-3 form-group m-t-20">
        <?php echo Html::submitButton(Yii::t('backend', 'Apply'), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
        <div class="clearfix"></div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
