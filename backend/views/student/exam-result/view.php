<?php

use common\models\ExamResult;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>
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
<div class="col-md-12">	
	<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'boxTools' => [
			'<i class="fa fa-plus add-new-exam-result m-r-10"></i>',
			Html::a('<i class="fa fa-print"></i>', ['print/evaluation', 'studentId' => $studentModel->id], ['target' => '_blank'])
		],
		'title' => 'Evaluations',
		'withBorder' => true,
	])
	?>
<?php Pjax::begin([
		'id' => 'student-exam-result-listing',
		'timeout' => 6000,
	]) ?>
	<?php
	echo GridView::widget([
	    'dataProvider' => $examResultDataProvider,
		'summary' => '',
	    'options' => ['class' => 'col-md-12'],
	    'tableOptions' => ['class' => 'table table-condensed'],
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
	<?php Pjax::end(); ?>		
	<?php LteBox::end() ?>
</div> 