<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Items Sold by Category';
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print', 'class'=> 'btn btn-box-tool']);
$this->params['show-all'] = $this->render('_button', [
    'model' => $searchModel
    ]);
?>

<div class="payments-index p-10">
    <div class="form-group form-inline">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <div class="box">
    <?php echo $this->render('_item-category', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
    </div>
</div>

<script>
$("#print").on("click", function() {
       var dateRange=$('#invoicelineitemsearch-daterange').val();
        var params = $.param({ 'InvoiceLineItemSearch[dateRange]': dateRange,
             });
        var url = '<?php echo Url::to(['item-category/print']); ?>?' + params;
        window.open(url,'_blank');
    });
$(document).ready(function(){
    $("#group-by-method").on("change", function() {
        var groupByMethod = $(this).is(":checked");
        var dateRange=$('#invoicelineitemsearch-daterange').val();
        var params = $.param({ 'InvoiceLineItemSearch[dateRange]': dateRange,
            'InvoiceLineItemSearch[groupByMethod]': (groupByMethod | 0) });
        var url = '<?php echo Url::to(['report/item-category']); ?>?' + params;
        $.pjax.reload({url:url,container:"#item-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
    $("#print").on("click", function() {
        var groupByMethod = $("#group-by-method").is(":checked");
        var dateRange = $('#paymentsearch-daterange').val();
        var params = $.param({ 'PaymentSearch[dateRange]': dateRange,
            'PaymentSearch[groupByMethod]': (groupByMethod | 0) });
        var url = '<?php echo Url::to(['payment/print']); ?>?' + params;
        window.open(url,'_blank');
    });
});
</script>
</script>
