<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
<style>
	.action-button {
		margin-top:-10px;
	}
</style>
    <?php $form = ActiveForm::begin([
        'id' => 'dashboard-search-form',
        'action' => ['index'],
        'method' => 'get'
    ]); ?>
<div class="form-group">
    <div class="dashboard-header-search">
        <div class="col-md-9">
           <?php 
           echo DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'dateRange',
            'convertFormat' => true,
            'initRangeExpr' => true,
	       'options' => [
                    'class' => 'form-control',
                    'readOnly' => true
                ],
            'pluginOptions' => [
                'autoApply' => true,
                'ranges' => [
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["moment().startOf('day').subtract(6, 'days')", 'moment()'],
                    Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["moment().startOf('day').subtract(29, 'days')", 'moment()'],
                    Yii::t('kvdrp', 'This Month') => ["moment().startOf('month')", "moment().endOf('month')"],
                    Yii::t('kvdrp', 'Last Month') => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                ],
                'locale' => [
                    'format' => 'M d, Y',
                ],
                'opens' => 'left',
                ],

            ]);
           ?>
        </div>
    </div>
</div>
    <?php ActiveForm::end(); ?>
<script>
$(document).ready(function () {
	$("#dashboard-search-form").on("submit", function () {
            var dateRange = $('#dashboardsearch-daterange').val();
            $.pjax.reload({container: "#dashboard", replace: false, timeout: 6000, data: $(this).serialize()});
            return false;
        });
    });
	$(document).on('change', '#dashboardsearch-daterange', function() {
		$("#dashboard-search-form").submit();
	});
</script>