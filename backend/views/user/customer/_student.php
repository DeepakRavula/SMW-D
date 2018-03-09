<?php 

use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;

?>
<div class="col-md-12">
	<a href="#" title="Add" class="add-new-student  pull-right"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin(['id' => 'customer-student-listing']) ?>
<?php
echo GridView::widget([
'dataProvider' => $dataProvider,
'summary' => false,
'emptyText' => false,
'rowOptions' => function ($model, $key, $index, $grid) {
    $url = Url::to(['student/view', 'id' => $model->id]);

    return ['data-url' => $url];
},
'options' => ['class' => 'col-md-12'],
'tableOptions' => ['class' => 'table table-bordered'],
'headerRowOptions' => ['class' => 'bg-light-gray'],
'columns' => [
    [
        'label' => 'Name',
        'value' => function ($data) {
            return !empty($data->fullName) ? $data->fullName : null;
        },
    ],
    'birth_date:date',
    [
        'label' => 'Customer Name',
        'value' => function ($data) {
            $fullName = !(empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;

            return $fullName;
        },
    ],
],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>