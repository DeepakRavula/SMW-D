<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
?>
<?php
	$results = [];
	if( ! empty($enrolments)){
		foreach($enrolments as $enrolment){
			$results[] = [
				'programName' => $enrolment->program->name,
				'teacherName' => $enrolment->teacher->publicIdentity,
				'startDate' => Yii::$app->formatter->asDate($enrolment->commencement_date),
				'endDate' => Yii::$app->formatter->asDate($enrolment->renewal_date),
			];
		}
	}

	if( ! empty($groupEnrolments)){
		foreach($groupEnrolments as $groupEnrolment){
			$results[] = [
				'programName' => $groupEnrolment->program->name,
				'teacherName' => $groupEnrolment->teacher->publicIdentity,
				'startDate' => Yii::$app->formatter->asDate($groupEnrolment->groupCourse->start_date),
				'endDate' => Yii::$app->formatter->asDate($groupEnrolment->groupCourse->end_date),
			];
		}
	}
	$enrolmentDataProvider = new ArrayDataProvider([
    'allModels' => $results,
    'sort' => [
        'attributes' => ['programName', 'teacherName', 'startDate','endDate'],
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
		'value' => 'programName',
		],
		[
		'label' => 'Teacher Name',
		'value' => 'teacherName',
		],
		[
		'label' => 'Start Date', 
		'value' => 'startDate',
		],
		[
		'label' => 'End Date', 
		'value' => 'endDate',
		],
    ]
]);
?>