<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
  .e1Div{
    right: 150px;
    top: -49px;
  }
  .apply-button {
    left: 920px;
    top: -49px;
  }
</style>
<div class="user-search">

    <?php $form = ActiveForm::begin([
		'id' => 'dashboard-search-form',
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="e1Div col-md-3">
   <?php 
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
	<div class=" col-md-3 apply-button">
	    <?php echo Html::submitButton(Yii::t('backend', 'Apply'), ['class' => 'btn btn-primary ']) ?>
		</div>
    <?php ActiveForm::end(); ?>
    </div>
<script>
    $(document).ready(function () {
$("#dashboard-search-form").on("submit", function () {
            var dateRange = $('#dashboardsearch-daterange').val();
            $.pjax.reload({container: "#dashboard", replace: false, timeout: 6000, data: $(this).serialize()});
            return false;
        });
    });
</script>