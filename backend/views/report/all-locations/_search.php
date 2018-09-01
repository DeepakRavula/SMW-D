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
    <?php $form = ActiveForm::begin([
        'id' => 'all-locations-search-form',
        'method' => 'get'
    ]); ?>
   	
	<div class="form-group">
           <?php 
           echo DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'dateRange',
            'id'=>'birthday-search' ,
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
                    'format' => 'M d,Y',
                ],
                'opens' => 'right',
                ],

            ]);
           ?>
        </div>
    <?php ActiveForm::end(); ?>
<script>
    $(document).ready(function () {
        $("#all-locations-search-form").on("submit", function () {
           var dateRange = $('#reportsearch-daterange').val();
        	$.pjax.reload({container: "#locations-listing", replace: false, timeout: 6000, data: $(this).serialize()});   
			return false
        });
    });
    $(document).off('change', '.reportsearch-daterange').on('change', '#reportsearch-daterange', function() {
        $("#all-locations-search-form").submit();
    });
</script>