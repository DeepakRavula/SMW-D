<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Program;

$this->title = 'Review Lessons';
?>
<div class="user-details-wrapper">
	<div class="row">
    <?php if((int) $courseModel->program->type === Program::TYPE_PRIVATE_PROGRAM) :?>  
	<div class="col-md-12">
		<p class="users-name"><?php echo $courseModel->enrolment->student->fullName; ?></p>
	</div>
    <?php endif; ?>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program Name">
		<i class="fa fa-music detail-icon"></i> <?=	$courseModel->program->name; ?>
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher name">
		<i class="fa fa-graduation-cap"></i> <?php echo $courseModel->teacher->publicIdentity;?>
	</div>	
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Commencement Date">
			<i class="fa fa-calendar"></i> <?php echo Yii::$app->formatter->asDate($courseModel->startDate)?>	
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Renewal Date">
			<i class="fa fa-calendar"></i> <?php echo Yii::$app->formatter->asDate($courseModel->endDate)?>	
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Time">
		<i class="fa fa-clock-o"></i> <?php 
		$fromTime = \DateTime::createFromFormat('H:i:s', $courseModel->fromTime);
		echo $fromTime->format('h:i A');?>	
	</div>
	</div>
    </div>
	<div class="clearfix"></div>	
	<div class="user-details-wrapper">
	<?php yii\widgets\Pjax::begin(); ?>
    <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			'date:date',
			[
				'label' => 'Time',
				'value' => function($data) {
					$time = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					return $time->format('h:i A');
                } 
			],
			[
				'label' => 'Teacher Name',
				'value' => function($data) {
					return $data->course->teacher->publicIdentity;	
                } 
			],
			[
				'label' => 'Conflict',
				'value' => function($data) {
                } 
			],
        ],
    ]); ?>

	<?php yii\widgets\Pjax::end(); ?>
		</div>
	<div class="form-group">
	<div class="p-10 text-center">
		<?= Html::a('Confirm', ['confirm', 'courseId' => $courseId], [
		'class' => 'btn btn-danger',
		'data' => [
			'method' => 'post',
		],
    ]) ?> 
        <?php if((int) $courseModel->program->type === Program::TYPE_PRIVATE_PROGRAM) :?>
		<?= Html::a('Cancel', ['student/view','id' => $courseModel->enrolment->studentId], ['class'=>'btn']); 	
		?>
        <?php else :?>
        <?= Html::a('Cancel', ['course/view','id' => $courseModel->id], ['class'=>'btn']); 	
		?>
       <?php endif; ?>
    </div>
</div>
