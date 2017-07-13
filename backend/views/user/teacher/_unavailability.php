<?php 

use yii\grid\GridView;
use yii\helpers\Url;

?>
<div>
<?php yii\widgets\Pjax::begin() ?>
<?php
echo GridView::widget([
'dataProvider' => $unavailabilityDataProvider,
'options' => ['class' => 'col-md-12'],
'tableOptions' => ['class' => 'table table-bordered m-t-15'],
'headerRowOptions' => ['class' => 'bg-light-gray'],
'columns' => [
    'fromDate:date',
	'toDate:date',
	'fromTime:time',
	'toTime:time'	
],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>
