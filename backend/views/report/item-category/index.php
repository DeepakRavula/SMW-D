<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Item Category';
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print']);
?>

<div class="payments-index p-10">
    <div class="form-group form-inline">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php echo $this->render('_item-category', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
</div>

<script>
$("#print").on("click", function() {
       var dateRange=$('#invoicelineitemsearch-daterange').val();
        var params = $.param({ 'InvoiceLineItemSearch[dateRange]': dateRange,
             });
        var url = '<?php echo Url::to(['item-category/print']); ?>?' + params;
        window.open(url,'_blank');
    });
</script>
