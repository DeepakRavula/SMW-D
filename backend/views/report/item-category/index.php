<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
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
    $(document).off('change', '#group-by-method').on('change', '#group-by-method', function(){
        var groupByMethod = $(this).is(":checked");
        var dateRange=$('#invoicelineitemsearch-daterange').val();
        var category=$('#invoicelineitemsearch-category').val();
        var params = $.param({ 'InvoiceLineItemSearch[dateRange]': dateRange, 'InvoiceLineItemSearch[category]': category,
            'InvoiceLineItemSearch[groupByMethod]': (groupByMethod | 0) });
        var url = '<?php echo Url::to(['report/item-category']); ?>?' + params;
        $.pjax.reload({url:url,container:"#item-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
    $(document).off('click', '#print').on('click', '#print', function(){
        var groupByMethod = $("#group-by-method").is(":checked");
        var dateRange=$('#invoicelineitemsearch-daterange').val();
        var category=$('#invoicelineitemsearch-category').val();
        var params = $.param({ 'InvoiceLineItemSearch[dateRange]': dateRange, 'InvoiceLineItemSearch[category]': category,
            'InvoiceLineItemSearch[groupByMethod]': (groupByMethod | 0) });
        var url = '<?php echo Url::to(['item-category/print']); ?>?' + params;
        window.open(url,'_blank');
    });
</script>
</script>
