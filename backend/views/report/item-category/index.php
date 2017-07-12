<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Item Category';
?>

<div class="payments-index p-10">
    <div id="print" class="btn btn-default pull-right m-t-20">
        <?= Html::a('<i class="fa fa-print"></i> Print') ?>
    </div>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo $this->render('_item-category', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
</div>

<script>
$("#print").on("click", function() {
        var fromDate = $('#from-date').val();
        var toDate = $('#to-date').val();
        var params = $.param({ 'InvoiceLineItemSearch[fromDate]': fromDate,
            'InvoiceLineItemSearch[toDate]': toDate });
        var url = '<?php echo Url::to(['item-category/print']); ?>?' + params;
        window.open(url,'_blank');
    });
</script>
