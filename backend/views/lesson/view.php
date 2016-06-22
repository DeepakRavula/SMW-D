<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */

$this->title = 'Lesson Details';
$this->params['breadcrumbs'][] = ['label' => 'Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-view">
	<div class="user-details-wrapper">
		<div class="col-md-12">
			<p class="users-name">
				<?php echo ! empty($model->enrolmentScheduleDay->enrolment->student->fullName) ? $model->enrolmentScheduleDay->enrolment->student->fullName : null ?>
				<em><small><?php echo ! empty(date("d-m-y", strtotime($model->date))) ? date("d-m-y", strtotime($model->date)) : null ?></small></em>
			</p>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program name">
			<i class="fa fa-music detail-icon"></i> <?php echo ! empty($model->enrolmentScheduleDay->enrolment->qualification->program->name) ? $model->enrolmentScheduleDay->enrolment->qualification->program->name : null ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Status">
			<i class="fa fa-info-circle detail-icon"></i> <?php echo $model->status($model) ?>
		</div>

		<div class="clearfix"></div>
		<div class="student-view">
			<?php
			// echo DetailView::widget([
			// 	'model' => $model,
			// 	'attributes' => [
			// 		'first_name',
			// 		'last_name',
			// 		'birth_date:date',
			// 		[
			// 			'label' => 'Customer Name',
			// 			'value' => !empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null,
			// 		],
			// 	],
			// ])
			?>
			<div class="col-md-12 action-btns">
				<?php echo Html::a('<i class="fa fa-pencil"></i> Update details', ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
		        <?php echo Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->id], [
		            'data' => [
		                'confirm' => 'Are you sure you want to delete this item?',
		                'method' => 'post',
		            ],
		        ]) ?>
		    </div>
		    <div class="clearfix"></div>
		</div>
	</div>


   <!--  <?php //echo DetailView::widget([
   //      'model' => $model,
   //      'attributes' => [
			// [
			// 	'label' => 'Student Name',
			// 	'value' => ! empty($model->enrolmentScheduleDay->enrolment->student->fullName) ? $model->enrolmentScheduleDay->enrolment->student->fullName : null,
			// ],
			// [
			// 	'label' => 'Program Name',
			// 	'value' => ! empty($model->enrolmentScheduleDay->enrolment->qualification->program->name) ? $model->enrolmentScheduleDay->enrolment->qualification->program->name : null,
			// ],
			// [
			// 	'label' => 'Status',
			// 	'value' => $model->status($model),
			// ],
			// [
			// 	'label' => 'Date',
			// 	'value' => ! empty(date("d-m-y", strtotime($model->date))) ? date("d-m-y", strtotime($model->date)) : null,
			// ],
   //      ],
   //  ]) ?>-->
    
</div>
