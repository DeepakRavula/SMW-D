<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Program;
use yii\widgets\Pjax;
use dosamigos\datetimepicker\DateTimePicker;

$this->title = 'Review Lessons';
?>
<div class="user-details-wrapper">
	<div class="row">
    <?php if((int) $courseModel->program->type === Program::TYPE_PRIVATE_PROGRAM) :?>  
	<div class="col-md-12">
		<p class="users-name"><?= $courseModel->enrolment->student->fullName; ?></p>
	</div>
    <?php endif; ?>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program Name">
		<i class="fa fa-music detail-icon"></i> <?=	$courseModel->program->name; ?>
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher name">
		<i class="fa fa-graduation-cap"></i> <?= $courseModel->teacher->publicIdentity;?>
	</div>	
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Commencement Date">
			<i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($courseModel->startDate)?>	
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Renewal Date">
			<i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($courseModel->endDate)?>	
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
	<?php
	$columns = [
		[
			'class'=>'kartik\grid\EditableColumn',
			'attribute'=>'date',    
			'format'=>'date',
			'headerOptions'=>['class'=>'kv-sticky-column'],
			'contentOptions'=>['class'=>'kv-sticky-column'],
			'editableOptions'=>[
				'header'=>'Lesson Date', 
				'size'=>'md',
				'inputType'=>\kartik\editable\Editable::INPUT_WIDGET,
				'widgetClass'=> '\yii\jui\DatePicker',
    		],
		],
	[
			'class'=>'kartik\grid\EditableColumn',
			'attribute'=>'time',  
			'value' => function ($model, $key, $index, $widget) {
				$lessonTime = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date)->format('H:i:s');
        		return Yii::$app->formatter->asTime($lessonTime);
		    },
			'headerOptions'=>['class'=>'kv-sticky-column'],
			'contentOptions'=>['class'=>'kv-sticky-column'],
			'editableOptions'=>[
				'header'=>'Lesson Time', 
				'size'=>'md',
				'inputType'=>\kartik\editable\Editable::INPUT_WIDGET,
				'widgetClass'=> 'dosamigos\datetimepicker\DateTimePicker',
				'options' => [
                    'clientOptions' => [
						'startView' => 1,
						'minView' => 0,
						'maxView' => 1,
						'pickDate' => false,
						'autoclose' => true,
						'format' => 'HH:ii P', 
					]
               	],
    		],
		],	
		[
			'label' => 'Conflict',
			'value' => function($data) {
				return $data->conflict;
			} 
		],
	];?>
	<?php Pjax::begin(); ?>
    <?= \kartik\grid\GridView::widget([
		'dataProvider'=>$lessonDataProvider,
		'columns'=>$columns
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
