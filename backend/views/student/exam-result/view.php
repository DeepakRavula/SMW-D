<?php

use yii\data\ActiveDataProvider;
use common\models\ExamResult;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="row p-10 student_eval_wrapper">
	<div id="new-exam-result" class="col-md-12">
		<h4 class="pull-left m-r-20">Exam History</h4>
		<a href="#" class="add-new-exam-result text-add-new m-r-10"><i class="fa fa-plus"></i></a>
		<?= Html::a('<i class="fa fa-print"></i>', ['print/evaluation', 'studentId' => $studentModel->id], ['class' => 'text-add-new', 'target' => '_blank']) ?>
		<div class="clearfix"></div>
	</div>
		<?php
		Modal::begin([
		    'header' => '<h4 class="m-0">Evaluations</h4>',
		    'id'=>'new-exam-result-modal',
		]);
		 echo $this->render('_form', [
				'model' => new ExamResult(),
		        'studentModel' => $studentModel,
		]);
		Modal::end();
		?>		
	<div>
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
	        [
				'label' => 'Exam Date',
				'value' => function($data) {
					return !empty($data->date) ? (new \DateTime($data->date))->format('M. d, Y') : null;
				}
			],
			'mark',
			'level',
			 [
				'label' => 'Program',
				'value' => function($data) {
					return !empty($data->programId) ? $data->program->name : null;
				}
			],
			[
				'label' => 'Type',
				'value' => function($data) {
					return !empty($data->type) ? $data->type : 'None';
				}
			],
			[
				'label' => 'Teacher',
				'value' => function($data) {
					return !empty($data->teacherId) ? $data->teacher->publicIdentity : null;
				}
			],
				    ],
	]);
	?>
	</div>
	<?php \yii\widgets\Pjax::end(); ?>	
</div>
