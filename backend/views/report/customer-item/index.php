<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Items By Customer';
$this->params['action-button'] = $this->render('_search', ['model' => $searchModel]); ?>

<div class="box">
    <?php echo $this->render('_item', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
</div>

<script>
$("#print").on("click", function() {
        var dateRange=$('#invoicelineitemsearch-daterange').val();
        var params = $.param({ 
            'InvoiceLineItemSearch[dateRange]': dateRange,
            'InvoiceLineItemSearch[isCustomerReport]': 1,
            'InvoiceLineItemSearch[customerId]': '<?= $searchModel->customerId; ?>'
        });
        var url = '<?php echo Url::to(['print/customer-items-print']); ?>?' + params;
        window.open(url,'_blank');
    });
</script>