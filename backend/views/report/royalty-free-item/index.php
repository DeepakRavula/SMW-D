<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
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
            var url = '<?php echo Url::to(['print/royalty-free']); ?>?' + params;
            window.open(url, '_blank');
        });
    });
</script>