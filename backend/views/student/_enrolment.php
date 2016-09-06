<?php

use yii\grid\GridView;
use common\models\Enrolment;
use common\models\GroupCourse;
use yii\data\ArrayDataProvider;
?>
<div class="col-md-12">
<h4 class="pull-left m-r-20">Enrolments</h4>
<a href="#" class="add-new-program text-add-new"><i class="fa fa-plus"></i></a>
<div class="clearfix"></div>
</div>

<div class="dn enrolment-create section-tab">
    <?php echo $this->render('//enrolment/_form', [
        'model' => $enrolmentModel,
    ]) ?>
</div>
<?php
$results = [];
if(! empty($privateLessons)){
	foreach($privateLessons as $privateLesson){
		$dayList = Enrolment::getWeekdaysList();
		$day = $dayList[$privateLesson->day];
		$duration = \DateTime::createFromFormat('h:i:s', $privateLesson->duration);
	    $privateLesson->duration = $duration->format('H:i');
		$results[] = [
			'program_id' => $privateLesson->program->name,
			'teacher_id' => $privateLesson->lessons[0]->teacher->publicIdentity,
			'day' => $day,
			'from_time' => Yii::$app->formatter->asTime($privateLesson->from_time),
			'duration' => $privateLesson->duration,
			'start_date' => Yii::$app->formatter->asDate($privateLesson->commencement_date),
			'end_date' => Yii::$app->formatter->asDate($privateLesson->renewal_date),
		];
	}
}

if(! empty($groupCourses)){
	foreach($groupCourses as $groupCourse){
		$dayList = GroupCourse::getWeekdaysList();
		$day = $dayList[$groupCourse->day];
		$fromTime = \DateTime::createFromFormat('Y-m-d H:i:s', $groupCourse->start_date);
		$duration = \DateTime::createFromFormat('h:i:s', $groupCourse->length);
		$results[] = [
			'program_id' => $groupCourse->program->name,
			'teacher_id' => $groupCourse->teacher->publicIdentity,
			'day' => $day,
			'from_time' => $fromTime->format('h:i A'),
			'duration' => $duration->format('H:i'),
			'start_date' => Yii::$app->formatter->asDate($groupCourse->start_date),
			'end_date' => Yii::$app->formatter->asDate($groupCourse->end_date),
		];
	}
}
?>
<?php
$enrolmentDataProvider = new ArrayDataProvider([
    'allModels' => $results,
    'sort' => [
        'attributes' => ['program_id', 'teacher_id', 'day', 'from_time', 'duration', 'start_date', 'end_date'],
    ],
]);
?>
<?php
echo GridView::widget([
	'dataProvider' => $enrolmentDataProvider,
	'tableOptions' =>['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray' ],
    'options' => ['class' => 'p-10'],
	'columns' => [
		[
		'label' => 'Program Name', 
		'value' => 'program_id',
		],
		[
		'label' => 'Teacher Name',
		'value' => 'teacher_id',
		],
		[
		'label' => 'Day', 
		'value' => 'day',
		],
		[
		'label' => 'From Time', 
		'value' => 'from_time',
		],
		[
		'label' => 'Duration', 
		'value' => 'duration',
		],
		[
		'label' => 'Start Date', 
		'value' => 'start_date',
		],
		[
		'label' => 'End Date', 
		'value' => 'end_date',
		],
    ]
]);
?>
