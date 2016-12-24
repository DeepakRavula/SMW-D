<?php

use yii\data\ActiveDataProvider;
use common\models\ExamResult;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div id="new-exam-result" class="col-md-12">
	<h4 class="pull-left m-r-20">Exam Results</h4>
	<a href="#" class="add-new-exam-result text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Add Exam Result</h4>',
    'id'=>'new-exam-result-modal',
]);
 echo $this->render('_form-exam-result', [
		'model' => new ExamResult(),
        'studentModel' => $studentModel,
]);
Modal::end();
?>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
	'id' => 'student-exam-result-listing',
	'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
    'dataProvider' => $examResultDataProvider,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        'date:date',
		'mark',
		'level',
		'program',
		[
			'label' => 'Type',
			'value' => !empty($data->type) ? $data->type : 'None'
		],
		[
			'label' => 'Teacher',
			'value' => !empty($data->teacherId) ? $data->teacher->publicIdentity : null
		]
    ],
]);
?>
</div>
<?php \yii\widgets\Pjax::end(); ?>
