<?php
/* @var $this yii\web\View */

use common\models\Location;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\TaxCode;
use common\models\TaxType;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

$this->title = 'Sales and Payments Summary';
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print', 'class' => 'btn btn-box-tool']);

?>
<div class="clearfix"></div>
<div class="col-md-4">	
    <?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Sales and Payment',
        'withBorder' => true,
    ])

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