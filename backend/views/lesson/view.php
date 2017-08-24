<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use common\models\Note;
use common\models\Lesson;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Lessons / Lesson Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON]);
?>
<div class="row">
	<div class="col-md-6">
		<?=
		$this->render('_details', [
			'model' => $model,
		]);
		?>
		<?=
		$this->render('_schedule', [
			'model' => $model,
		]);
		?>
	</div>
	<div class="col-md-6">
		<?=
		$this->render('_student', [
			'model' => $model,
		]);
		?>
		<?=
		$this->render('_attendance', [
			'model' => $model,
		]);
		?>	
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
				<?php
				$noteContent = $this->render('note/view', [
					'model' => new Note(),
					'noteDataProvider' => $noteDataProvider
				]);

				$logContent = $this->render('log', [
					'model' => $model,
				]);

				$items = [
						[
						'label' => 'Comments',
						'content' => $noteContent,
					],
						[
						'label' => 'History',
						'content' => $logContent,
					],
				];
				?>
				<?php
				echo Tabs::widget([
					'items' => $items,
				]);
				?>
			</div>
		</div>	
</div>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit Details</h4>',
    'id' => 'classroom-edit-modal',
]); ?>
<?= $this->render('classroom/_form', [
	'model' => $model,
]);?>
<?php Modal::end(); ?>

<script>
 $(document).ready(function() {
 	$(document).on('click', '.edit-lesson-detail', function () {
		$('#classroom-edit-modal').modal('show');
		return false;
	});
	$(document).on('click', '.lesson-detail-cancel', function () {
		$('#classroom-edit-modal').modal('hide');
		return false;
	});
	$(document).on('beforeSubmit', '#classroom-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   	{
					$('#classroom-edit-modal').modal('hide');
					$.pjax.reload({container: '#lesson-detail', timeout: 6000});
				}
			}
		});
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
				}
			}
		});
		return false;
	});
});
</script>
