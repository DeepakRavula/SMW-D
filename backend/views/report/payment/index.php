<?php

use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments';
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print']);
$this->params['show-all'] = $this->render('_button', [
    'model' => $searchModel
    ]);
?>

<div class="payments-index p-10">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo $this->render('_payment', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
	
</div>

<script>
$(document).ready(function(){
    $("#group-by-method").on("change", function() {
        var groupByMethod = $(this).is(":checked");
        var dateRange = $('#paymentsearch-daterange').val();
        var params = $.param({ 'PaymentSearch[dateRange]': dateRange,
            'PaymentSearch[groupByMethod]': (groupByMethod | 0) });
        var url = '<?php echo Url::to(['report/payment']); ?>?' + params;
        $.pjax.reload({url:url,container:"#payment-listing",replace:false,  timeout: 4000});  //Reload GridView
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