<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="payments-index p-10">
    <div class="payment-search">
        <div class="form-inline form-group">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
        <div class="box">  
            <?php echo $this->render('_payment', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider, 'paymentsAmount' => $paymentsAmount]); ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $("#group-by-method").on("change", function() {
        var groupByMethod = $(this).is(":checked");
        var dateRange = $('#paymentreportsearch-daterange').val();
        var params = $.param({ 'PaymentReportSearch[dateRange]': dateRange,
            'PaymentReportSearch[groupByMethod]': (groupByMethod | 0) });
        var url = '<?php echo Url::to(['report/payment']); ?>?' + params;
        $.pjax.reload({url:url,container:"#payment-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
    $("#print").on("click", function() {
        var groupByMethod = $("#group-by-method").is(":checked");
        var dateRange = $('#paymentreportsearch-daterange').val();
        var params = $.param({ 'PaymentReportSearch[dateRange]': dateRange,
            'PaymentReportSearch[groupByMethod]': (groupByMethod | 0) });
        var url = '<?php echo Url::to(['payment/print']); ?>?' + params;
        window.open(url,'_blank');
    });
});
</script>