<?php

use yii\helpers\Url;
use common\models\Lesson;
use backend\models\search\LessonSearch;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Location;
use common\models\UserProfile;
use common\models\Student;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Unscheduled Lessons';
$this->params['show-all'] = $this->render('_button', [
    'searchModel' => $searchModel
]);
?>
<div class="grid-row-open p-10">
<?php Pjax::begin([
    'enablePushState' => false,
    'timeout' => 6000,
	'id' => 'unscheduled-lesson-listing']); ?>
	<?php
	$columns	 = [
		[
			'label' => 'Student',
			'attribute' => 'student',
			'value' => function ($data) {
				return !empty($data->course->enrolment->student->fullName) ? substr($data->course->enrolment->student->fullName, 0, 21)
						: null;
			},
			'contentOptions' => ['class' => 'text-ellipsis display-22'],
		],
		[
			'label' => 'Program',
			'attribute' => 'program',
			'value' => function ($data) {
				return !empty($data->course->program->name) ? $data->course->program->name : null;
			},
			'contentOptions' => ['class' => 'text-ellipsis display-22'],
		],
		[
			'label' => 'Teacher',
			'attribute' => 'teacher',
			'value' => function ($data) {
				return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity
						: null;
			},
			'contentOptions' => ['class' => 'text-ellipsis display-22'],
		],
		[
			'label' => 'Duration',
			'value' => function ($data) {
				return !empty($data->duration) ? (new \DateTime($data->duration))->format('H:i')
						: null;
			},
			'contentOptions' => ['class' => 'text-ellipsis display-6'],
		],
		[
			'label' => 'Date',
			'value' => function ($data) {
				$date = Yii::$app->formatter->asDate($data->date);

				return !empty($date) ? $date : null;
			},
			'contentOptions' => ['class' => 'text-ellipsis display-14'],
		],
		[
			'label' => 'Expiry Date',
			'value' => function ($data) {
				if (!empty($data->privateLesson->expiryDate)) {
					$date = Yii::$app->formatter->asDate($data->privateLesson->expiryDate);
				}
				return !empty($date) ? $date : null;
			},
			'contentOptions' => ['class' => 'text-ellipsis display-14'],
		],
	];
	?>
    <div class="box">
		<?php
		echo KartikGridView::widget([
			'dataProvider' => $dataProvider,
			'options' => ['id' => 'lesson-index-1'],
			'filterModel' => $searchModel,
			'summary' => "Showing {begin} - {end} of {totalCount} items",
			'rowOptions' => function ($model, $key, $index, $grid) {
				$url = Url::to(['lesson/view', 'id' => $model->id]);

				return ['data-url' => $url];
			},
			'tableOptions' => ['class' => 'table table-bordered'],
			'headerRowOptions' => ['class' => 'bg-light-gray'],
			'columns' => $columns,
			'toolbar' =>  [
				'{export}',
				'{toggleData}'
			],
			'export' => [
				'fontAwesome' => true,
			],  
			'panel' => [
					'type' => GridView::TYPE_DEFAULT
				],
			'toggleDataOptions' => ['minCount' => 20],
		]);
		?>
	</div>
<?php Pjax::end(); ?>
</div>

<script>
$(document).off('change', '#unscheduledlessonsearch-showall').on('change', '#unscheduledlessonsearch-showall', function () {
      	var showAllExpiredLesson = $(this).is(":checked");
		var student_search = $("input[name*='UnscheduledLessonSearch[student]").val();
		var program_search = $("input[name*='UnscheduledLessonSearch[program]").val();
        var teacher_search = $("input[name*='UnscheduledLessonSearch[teacher]").val();
    	var params = $.param({ 'UnscheduledLessonSearch[student]':student_search, 'UnscheduledLessonSearch[program]':program_search, 'UnscheduledLessonSearch[teacher]':teacher_search, 'UnscheduledLessonSearch[showAll]': (showAllExpiredLesson | 0) });
      	var url = "<?php echo Url::to(['unscheduled-lesson/index']); ?>?"+params;
        $.pjax.reload({url: url, container: "#unscheduled-lesson-listing", replace: false, timeout: 4000});  //Reload GridView
});
</script>
