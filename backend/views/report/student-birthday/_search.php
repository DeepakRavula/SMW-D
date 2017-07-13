<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
use kartik\grid\GridView;

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
   	<style>
		#w20-container table > tbody > tr.info > td{
			padding:8px;
			background:#fff;
		}
		.kv-page-summary, .table > tbody + tbody{
			border: 0;
		}
		.table-striped > tbody > tr:nth-of-type(odd){
			background: transparent;
		}
	</style>
	<div class="row">
		<div class="col-md-3">
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
        <div class="col-md-2">
	   <?php echo Html::submitButton(Yii::t('backend', 'Apply'), ['class' => 'btn btn-primary']) ?>
        </div>
        <div id="print" class="btn btn-default col-md-0">
        <?= Html::a('<i class="fa fa-print"></i> Print') ?>
   </div>        	</div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).ready(function () {
        $("#birthday-search-form").on("submit", function () {
           var dateRange = $('#birthday-daterange').val();
        	$.pjax.reload({container: "#birthday-listing", replace: false, timeout: 6000, data: $(this).serialize()});           return false
            var params = $.param({ 'StudentBirthdaySearch[fromDate]': fromDate,
            'StudentBirthdaySearch[toDate]': toDate});
            var url = '<?= Url::to(['user/print', 'id' => $model->id]); ?>&' + params;
            $('#print-btn').attr('href', url);	
        return false;
        });
    });
</script>
