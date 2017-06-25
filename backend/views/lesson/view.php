<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use common\models\Note;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Lessons / Lesson Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
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
		<div class="tabbable-panel">
			<div class="tabbable-line">
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
</div>
