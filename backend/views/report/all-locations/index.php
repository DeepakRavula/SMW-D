<?php

use yii\helpers\Url;
use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use common\models\LocationDebt;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'All Locations';
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i>', '#', ['id' => 'print', 'class' => 'btn btn-box-tool']);
?>
<div class="form-group form-inline">
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<?php echo $this->render('_all-locations', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider,]); ?>
<script>
        $(document).on("click", "#print", function () {
            var dateRange = $('#reportsearch-daterange').val();
            var params = $.param({'ReportSearch[dateRange]': dateRange});
            var url = '<?php echo Url::to(['print/all-locations']); ?>?' + params;
            window.open(url, '_blank');
        });
</script>
