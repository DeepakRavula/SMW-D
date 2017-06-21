<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use common\models\Note;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Lesson Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>
 <div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$lessonContent = $this->render('_view', [
    'model' => $model,
]);

$noteContent = $this->render('note/view', [
	'model' => new Note(),
	'noteDataProvider' => $noteDataProvider
]);

$studentContent = $this->render('student/view', [
	'studentDataProvider' => $studentDataProvider,
        'lessonModel' => $model,
]);

$logContent = $this->render('log', [
	'model' => $model,
]);

$items = [
	[
		'label' => 'Details',
		'content' => $lessonContent,
		'options' => [
				'id' => 'details',
			],
	],
	[
		'label' => 'Notes',
		'content' => $noteContent,
		'options' => [
			'id' => 'note',
		],
	],
	[
		'label' => 'Logs',
		'content' => $logContent,
		'options' => [
			'id' => 'log',
		],
	],
];
$groupLesson = [
	[
		'label' => 'Students',
		'content' => $studentContent,
		'options' => [
			'id' => 'student',
		],
	],
];
if ($model->course->program->isGroup()) {
	$items = array_merge($items, $groupLesson);
}
?>
<?php
	echo Tabs::widget([
		'items' => $items,
	]);
?>
    </div>
 </div>
<script>
 $(document).ready(function() {
	$(document).on('click', '#lesson-note', function (e) {
		$('#note-content').val('');
		$('#lesson-note-modal').modal('show');
		return false;
  	});
	$(document).on('beforeSubmit', '#lesson-note-form', function (e) {
		$.ajax({
			url    : '<?= Url::to(['note/create', 'instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_LESSON]); ?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('.lesson-note-content').html(response.data);
					$('#lesson-note-modal').modal('hide');
				}else
				{
				 $('#lesson-note-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});
});
</script>


