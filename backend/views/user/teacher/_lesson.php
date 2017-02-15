<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use common\models\Lesson;
use yii\data\ActiveDataProvider;
?>
<style>	
.diff_color{
		background: #f9f9f9 !important;
    color: #333;
}
    #unscheduled .grid-row-open{
        padding:15px !important;
    }
    #user-note{
    	padding:15px;
    }
.user-note-content .empty{
	padding:15px;
}
</style>
<?php
$locationId = Yii::$app->session->get('location_id');
$lessonDate = new \DateTime($model->date);
$teacherLessons = Lesson::find()
		->location($locationId)
		->where(['lesson.teacherId' => $model->teacherId])
		->notDraft()
		->notDeleted()
		->between($lessonDate, $lessonDate);

	$teacherLessonDataProvider = new ActiveDataProvider([
        'query' => $teacherLessons,
		'pagination' => false,
    ]);
$totalDuration	 = 0;
$lessonTotal = 0;
$totalCost = 0;
$count			 = $teacherLessonDataProvider->getCount();
if (!empty($teacherLessonDataProvider->getModels())) {
	foreach ($teacherLessonDataProvider->getModels() as $key => $val) {
		$duration		 = \DateTime::createFromFormat('H:i:s', $val->duration);
		$hours			 = $duration->format('H');
		$minutes		 = $duration->format('i');
		$lessonDuration	 = $hours + ($minutes / 60);
		$totalDuration += $lessonDuration;
		if($val->course->program->isPrivate()) {
			$lessonTotal = $lessonDuration * $val->course->program->rate; 
		} else {
			$lessonTotal  = $val->course->program->rate / $val->getGroupLessonCount();
		}
		$totalCost += $lessonTotal;
	}
}
?>
<div>
	<?php
	echo GridView::widget([
		'id' => 'teacher-lesson',
		'dataProvider' => $teacherLessonDataProvider,
		'footerRowOptions' => ['style' => 'font-weight:bold;text-align: right;'],
		'showFooter' => true,
		'tableOptions' => ['class' => 'table table-bordered		 m-b-0 table-condensed'],
		'headerRowOptions' => ['class' => 'bg-light-gray diff_color'],
		'columns' => [
			[
				'label' => 'Time',
				'value' => function ($data) {
					return !empty($data->date) ? Yii::$app->formatter->asTime($data->date) : null;
				},
			],
			[
				'label' => 'Program Name',
				'value' => function ($data) {
					return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
				},
			],
			[
				'label' => 'Student Name',
				'value' => function ($data) {
					return !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
				},
			],
			[
				'label' => 'Duration (hrs)',
				'value' => function ($data) {
					return $data->getDuration();
				},
				'headerOptions' => ['class' => 'text-right'],
				'contentOptions' => ['class' => 'text-right'],
				'footer' => $totalDuration,
			],
			[
				'label' => 'Rate',
				'value' => function ($data) {
					return $data->course->program->rate;	
				},
				'headerOptions' => ['class' => 'text-right'],
				'contentOptions' => ['class' => 'text-right'],
			],
			[
				'label' => 'Cost',
				'value' => function ($data) {
					if($data->course->program->isPrivate()) {
						$cost = $data->getDuration() * $data->course->program->rate;	
					} else {
						$cost = $data->course->program->rate / $data->getGroupLessonCount();
					}
					return $cost;
				},
				'headerOptions' => ['class' => 'text-right'],
				'contentOptions' => ['class' => 'text-right'],
				'footer' => $totalCost,
			],
		],
	]);
	?>
</div>
