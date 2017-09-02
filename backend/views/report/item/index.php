<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Items';
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print']);
?>

<div class="payment-search">
    <div class="form-inline form-group">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div></div>
    <?php echo $this->render('_item', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>


<script>
$("#print").on("click", function() {
        var dateRange=$('#invoicelineitemsearch-daterange').val();
        var params = $.param({ 'InvoiceLineItemSearch[dateRange]': dateRange,
             });
        var url = '<?php echo Url::to(['item/print']); ?>?' + params;
        window.open(url,'_blank');
    });
</script>