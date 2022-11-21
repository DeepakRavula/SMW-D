<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="payment-search">
    <div class="form-inline form-group">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div></div>
<div class="box">
    <?php echo $this->render('_item', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
</div>

<script>
$("#print").on("click", function() {
        var dateRange=$('#invoicelineitemsearch-daterange').val();
        var params = $.param({ 'InvoiceLineItemSearch[dateRange]': dateRange,
             });
        var url = '<?php echo Url::to(['item/print']); ?>?' + params;
        window.open(url,'_blank');
    });
</script>