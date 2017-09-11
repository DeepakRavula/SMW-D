<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Royalty Free Items';
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print', 'class' => 'btn btn-box-tool']);

?>
<div class="form-group form-inline">
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<div class="clearfix"></div>
<div class="box">
<?php echo $this->render('_royaltyfree', ['model' => $searchModel, 'royaltyFreeDataProvider' => $royaltyFreeDataProvider,]); ?>
</div>
<script>
    $(document).ready(function () {
        $(document).on("click", "#print", function () {
            var dateRange = $('#reportsearch-daterange').val();
            var params = $.param({'ReportSearch[dateRange]': dateRange});
            var url = '<?php echo Url::to(['report/royalty-free-print']); ?>?' + params;
            window.open(url, '_blank');
        });
    });
</script>