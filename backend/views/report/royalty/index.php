<?php
/* @var $this yii\web\View */

use common\models\Location;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\TaxCode;
use common\models\TaxType;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

$this->title = 'Royalty';
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print', 'class' => 'btn btn-box-tool']);

?>
<div class="col-xs-12 col-md-6 form-group form-inline">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<div class="clearfix"></div>
<div class="col-md-4">	
    <?php
    $locationId = Location::findOne(['slug' => \Yii::$app->location])->name;
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Royalty for' . ' '.$locationId,
        'withBorder' => true,
    ])

    ?>
    <?php
    echo $this->render('_royalty', [
        'searchModel' => $searchModel,
        'invoiceTaxTotal' => $invoiceTaxTotal,
        'payments' => $payments,
        'royaltyFreeAmount' => $royaltyFreeAmount,
        'giftCardPayments' => $giftCardPayments,
    ]);

    ?>
<?php LteBox::end() ?>
</div> 
<div class="clearfix"></div>
<script>
    $(document).ready(function () {
        $(document).on("click", "#print", function () {
            var dateRange = $('#reportsearch-daterange').val();
            var params = $.param({'ReportSearch[dateRange]': dateRange});
            var url = '<?php echo Url::to(['print/royalty']); ?>?' + params;
            window.open(url, '_blank');
        });
    });
</script>