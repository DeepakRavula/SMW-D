<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Review Lessons';
?>
<div class="user-details-wrapper">
	<div class="row">
	<div class="col-md-12">
		<p class="users-name"><?php echo $model->fullName; ?></p>
	</div>
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
		<?= Html::a('Confirm', ['lesson-confirm', 'id' => $model->id, 'enrolmentId' => $enrolmentId], [
		'class' => 'btn btn-danger',
		'data' => [
			'method' => 'post',
		],
]) ?>
		<?= Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	
		?>
    </div>
</div>
