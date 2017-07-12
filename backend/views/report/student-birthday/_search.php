<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */

?>
<div class="user-search">
    <?php $form = ActiveForm::begin([
		'id' => 'birthday-search-form',
        //'action' => ['report/student-birthday/'],
        'method' => 'get'
    ]); ?>
    <div class="row">
    <div class="col-md-5">
           <?php 
           echo DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'dateRange',
            'id'=>'birthday-search' , 
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
                'opens' => 'right',
                ],

            ]);
           ?>
        </div>
	   <?php echo Html::submitButton(Yii::t('backend', 'Apply'), ['class' => 'btn btn-primary']) ?>
	</div>
    <?php ActiveForm::end(); ?>
</div>
