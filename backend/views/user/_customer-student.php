<?php 


use yii\grid\GridView;
use yii\helpers\Url;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Students </h4> 
	<a href="#" class="add-new-student text-add-new"><i class="fa fa-plus"></i></a>
	<?php //echo Html::a('<i class="fa fa-plus-circle"></i> Add new student', ['student/create'], ['class' => 'add-new-program text-add-new'])?>
	<div class="clearfix"></div>
</div>
<div class="dn show-create-student-form">
	<?php echo $this->render('//student/create', [
		'model' => $student,
		'customer' => $model,
	]) ?>
</div>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin() ?>
<?php
echo GridView::widget([
'dataProvider' => $dataProvider,
'rowOptions' => function ($model, $key, $index, $grid) {
    $url = Url::to(['student/view', 'id' => $model->id]);
return ['data-url' => $url];
},
'options' => ['class'=>'col-md-12'],
'tableOptions' =>['class' => 'table table-bordered'],
'headerRowOptions' => ['class' => 'bg-light-gray' ],
'columns' => [
	[
		'label' => 'Name',
		'value' => function($data) {
			return !empty($data->fullName) ? $data->fullName : null;
		},
	],
	'birth_date:date',
	[
		'label' => 'Customer Name',
		'value' => function($data) {
			$fullName = !(empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;
			return $fullName;
		}
	],
],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>
