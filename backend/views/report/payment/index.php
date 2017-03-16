<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments';
?>

<div class="payments-index p-10">
    <div id="print" class="btn btn-default pull-right m-t-20">
        <?= Html::a('<i class="fa fa-print"></i> Print') ?>
    </div>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo $this->render('_payment', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
	
</div>

<script>
$(document).ready(function(){
    $("#group-by-method").on("change", function() {
        var groupByMethod = $(this).is(":checked");
        var fromDate = $('#from-date').val();
        var toDate = $('#to-date').val();
        var params = $.param({ 'PaymentSearch[fromDate]': fromDate,
            'PaymentSearch[toDate]': toDate, 'PaymentSearch[groupByMethod]': (groupByMethod | 0) });
        var url = '<?php echo Url::to(['report/payment']); ?>?' + params;
        $.pjax.reload({url:url,container:"#payment-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
    $("#print").on("click", function() {
        var groupByMethod = $("#group-by-method").is(":checked");
        var fromDate = $('#from-date').val();
        var toDate = $('#to-date').val();
        var params = $.param({ 'PaymentSearch[fromDate]': fromDate,
            'PaymentSearch[toDate]': toDate, 'PaymentSearch[groupByMethod]': (groupByMethod | 0) });
        var url = '<?php echo Url::to(['payment/print']); ?>?' + params;
        window.open(url,'_blank');
    });
});
</script>