<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tax Collected';
$this->params['show-all'] = $this->render('_button', ['model' => $searchModel]);
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print', 'class' => 'btn btn-box-tool']);

?>

<div class="form-inline form-group">
    <?= $this->render('_search', ['model' => $searchModel]); ?>
</div>

<?= $this->render('_taxcollected', [
    'searchModel' => $searchModel, 
    'taxDataProvider' => $taxDataProvider,
    'taxSum' => $taxSum,
    'subtotalSum' => $subtotalSum,
    'totalSum' => $totalSum
]); ?>
    
<script>
    $(document).ready(function () {
        $("#reportsearch-summarizeresults").on("change", function () {
            var summariesOnly = $(this).is(":checked");
            var dateRage = $('#reportsearch-daterange').val();
            var params = $.param({'ReportSearch[summarizeResults]': (summariesOnly | 0),
                'ReportSearch[dateRange]': dateRage});
            var url = '<?php echo Url::to(['report/tax-collected']); ?>?' + params;
            $.pjax.reload({url: url, container: "#tax-grid", replace: false, timeout: 6000});
        });
    });
    $(document).on("click", "#print", function () {
        var summariesOnly = $("#reportsearch-summarizeresults").is(":checked");
        var dateRange = $('#reportsearch-daterange').val();
        var params = $.param({'ReportSearch[summarizeResults]': (summariesOnly | 0),
            'ReportSearch[dateRange]': dateRange});
        var url = '<?php echo Url::to(['print/tax-collected']); ?>?' + params;
        window.open(url, '_blank');
    });
</script>