<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\models\InvoiceLineItem;

?>
<div class="col-md-12">
    <?php $form = ActiveForm::begin([
        'id' => 'time-voucher-search-form',
    ]); ?>
    
    <div class="row">
        <div class="col-md-3 form-group">
            <?= DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'dateRange',
                'convertFormat' => true,
                'initRangeExpr' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'ranges' => [
                        Yii::t('kvdrp', 'Today') => ["moment().startOf('day')", "moment()"],
                        Yii::t('kvdrp', 'Tomorrow') => ["moment().startOf('day').add(1,'days')", "moment().endOf('day').add(1,'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 7]) => ["moment().startOf('day')", "moment().endOf('day').add(6, 'days')"],
                        Yii::t('kvdrp', 'Next {n} Days', ['n' => 30]) => ["moment().startOf('day')", "moment().endOf('day').add(29, 'days')"],
                    ],
                    'locale' => [
                        'format' => 'M d,Y',
                    ],
                    'opens' => 'right',
                ],
            ]); ?>
        </div>
        <div class="col-md-1 form-group">
            <?= Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'search', 'class' => 'btn btn-primary']) ?>
        </div>
        <div class="col-md-1 form-group">
            <?= Html::a('<i class="fa fa-print"></i> Print', null, ['id' => 'time-voucher-print-btn', 'class' => 'btn btn-default m-r-10']) ?>
        </div>
        <div class="pull-right checkbox">
            <?= $form->field($searchModel, 'summariseReport')->checkbox(['data-pjax' => true]); ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?= $this->render('_cost-time-voucher-content', [
    'model' => $model,
    'searchModel' => $searchModel,
    'timeVoucherDataProvider' => $timeVoucherDataProvider,
]); ?>


<script>
    $(document).on('beforeSubmit', '#time-voucher-search-form', function () {
        var dateRange = $('#invoicesearch-daterange').val();
        var params = $.param({ 'InvoiceSearch[dateRange]': dateRange});
        $.pjax.reload({container: "#time-voucher-grid", replace: false, timeout: 6000, data: $(this).serialize()});
        var url = '<?= Url::to(['print/time-voucher', 'id' => $model->id]); ?>&' + params;
        $('#time-voucher-print-btn').attr('href', url);
        return false;
    });

    $(document).on('click', '#time-voucher-print-btn', function () {
        var dateRange = $('#invoicesearch-daterange').val();
        var params = $.param({ 'InvoiceSearch[dateRange]': dateRange});
        var url = '<?= Url::to(['print/time-voucher', 'id' => $model->id]); ?>&' + params;
        window.open(url, '_blank');
        return false;
    });

    $("#invoicesearch-summarisereport").on("change", function() {
        var summariesOnly = $(this).is(":checked");
        var dateRange = $('#invoicesearch-daterange').val();
        var params = $.param({ 'InvoiceSearch[dateRange]': dateRange,'InvoiceSearch[summariseReport]': (summariesOnly | 0) });
        var url = '<?php echo Url::to(['user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $model->id]); ?>&' + params;
        $.pjax.reload({url:url,container:"#time-voucher-grid",replace:false,  timeout: 4000});  //Reload GridView
		var printUrl = '<?= Url::to(['print/time-voucher', 'id' => $model->id]); ?>&' + params;
		$('#time-voucher-print-btn').attr('href', printUrl);
    });
</script>