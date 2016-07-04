<?php 


use yii\grid\GridView;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Students </h4> 
	<a href="#" class="add-new-student text-add-new"><i class="fa fa-plus-circle"></i> Add new student</a>
	<?php //echo Html::a('<i class="fa fa-plus-circle"></i> Add new student', ['student/create'], ['class' => 'add-new-program text-add-new'])?>
	<div class="clearfix"></div>
</div>
<div class="dn show-create-student-form">
	<?php echo $this->render('//student/create', [
		'model' => $student,
		'customer' => $model,
	]) ?>
</div>

<?php
echo GridView::widget([
'dataProvider' => $dataProvider,
'rowOptions' => function ($model, $key, $index, $grid) {
	$u= \yii\helpers\StringHelper::basename(get_class($model));
	$u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
	return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
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
	//['class' => 'yii\grid\ActionColumn', 'controller' => 'student'],
],
]);
?>
<div class="clearfix"></div>

