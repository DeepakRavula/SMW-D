<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>
    <?php $form = ActiveForm::begin([
        'id' => 'discount-search-form',
        'action' => ['report/discount'],
        'method' => 'get'
    ]); ?>
        <div class="form-group">
            <?php echo DateRangePicker::widget([
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
                    'format' => 'M d,Y',
                ],
                'opens' => 'left',
                ],

            ]);
           ?>
        </div>
    <?php ActiveForm::end(); ?>
    
<script>
$(document).on('change', '#discountsearch-daterange', function() {
    $("#discount-search-form").submit();
});

$(document).on("submit", '#discount-search-form', function (e) {
    e.preventDefault();
    $.pjax.reload({container: "#discount-report", replace: false, timeout: 6000, data: $(this).serialize()});
    return false;
});
</script>