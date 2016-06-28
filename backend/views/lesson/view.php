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
			</p>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program name">
			<i class="fa fa-music detail-icon"></i> <?php echo ! empty($model->enrolmentScheduleDay->enrolment->qualification->program->name) ? $model->enrolmentScheduleDay->enrolment->qualification->program->name : null ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Status">
			<i class="fa fa-info-circle detail-icon"></i> <?php 
				$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
				$currentDate = new \DateTime();

				if ($lessonDate <= $currentDate) {
					$status = 'Completed';
				} else {
					$status = 'Scheduled';
				}

			echo $status ?>
		</div>

		<div class="clearfix"></div>
		<div><h5>Teacher Name:
		<?php echo !empty($model->enrolmentScheduleDay->enrolment->qualification->teacher->publicIdentity) ? $model->enrolmentScheduleDay->enrolment->qualification->teacher->publicIdentity : null;?>
        <br>
        Lesson Date:
        <em><small><?php echo ! empty(date("d-m-Y", strtotime($model->date))) ? date("d-m-Y g:i a", strtotime($model->date)) : null ?></small></em>
        </h5></div>
		<div class="student-view">
			<div class="col-md-12 action-btns">
				<?php echo Html::a('<i class="fa fa-pencil"></i> Update details', ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
		        <?php echo Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->id], ['class' => 'm-r-20',
		            'data' => [
		                'confirm' => 'Are you sure you want to delete this item?',
		                'method' => 'post',
		            ],
		        ]) ?>
				<?php echo Html::a('<i class="fa fa-dollar"></i> Invoice this Lesson', ['invoice', 'id' => $model->id], ['class' => 'm-r-20']) ?>
		    </div>
		    <div class="clearfix"></div>
		</div>
	</div>
</div>
