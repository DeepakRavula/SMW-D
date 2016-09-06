<?php
use yii\helpers\Html;
?>
<?php if(! empty($enrolmentModel)):?>
<div class="smw-box col-md-3 m-l-10 m-b-20">
<h4>Student Name: <?= $enrolmentModel->student->fullName;?></h4>
<h4>Program Name : <?= $enrolmentModel->program->name;?></h4>
<h4> Future Lesson Count : <?= $enrolmentModel->getFutureLessonsCount($enrolmentModel->id)?> </h4>
</div>
<div class="clearfix"></div>
<?php endif;?>
<?= Html::a('Delete', ['delete', 'id' => $model->id], [
		'class' => 'btn btn-danger',
		'data' => [
			'confirm' => 'Are you sure you want to delete this item?',
			'method' => 'post',
		],
]) ?>
<?= Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	?>
