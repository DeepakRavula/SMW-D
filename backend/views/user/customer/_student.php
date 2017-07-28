<?php 

use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;

?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Students </h4> 
	<a href="#" class="add-new-student text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
	<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Add Student</h4>',
		'id'=>'student-create-modal',
	]);
	echo $this->render('_form-student', [
        'model' => $student,
        'customer' => $model,
    ]);
	Modal::end();
	?>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin(['id' => 'customer-student-listing']) ?>
<?php
echo GridView::widget([
'dataProvider' => $dataProvider,
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
