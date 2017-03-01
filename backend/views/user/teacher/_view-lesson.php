<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Lesson;
?>
<div class="col-md-12">
	<?php
	$form = ActiveForm::begin([
			'id' => 'teacher-lesson-search-form',
	]);
	?>
	<style>
		#w20-container table > tbody > tr.info > td{
			padding:8px;
			background:#fff;
		}
		.bg-light-gray-1{
			background: #f5ecec;
		}
	</style>
	<div class="row">
		<div class="col-md-2">
			<?php
			echo $form->field($searchModel, 'fromDate')->widget(DatePicker::classname(), [
				'options' => [
					'class' => 'form-control',
				],
			])
			?>
		</div>
		<div class="col-md-2">
			<?php
			echo $form->field($searchModel, 'toDate')->widget(DatePicker::classname(), [
				'options' => [
					'class' => 'form-control',
				],
			])
			?>
		</div>
		<div class="col-md-2 form-group p-t-5">
			<Br>
			<?php echo Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'search', 'class' => 'btn btn-primary']) ?>
		</div>
		<div class="col-md-4 m-t-20">
			<div class="schedule-index">
			 <?= $form->field($searchModel, 'summariseReport')->checkbox(['data-pjax' => true]); ?>
        	</div>
		</div>
		<div class="col-md-2 m-t-25">
			<?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['id' => 'print-btn', 'class' => 'btn btn-default btn-sm pull-right m-r-10', 'target' => '_blank']) ?>

		</div>
		<div class="clearfix"></div>
	</div>
</div>
<?php ActiveForm::end(); ?>

<?php
if(!$searchModel->summariseReport) {
$columns = [
		[
		'value' => function ($data) {
			if( ! empty($data->date)) {
    			$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
			    return $lessonDate->format('l, F jS, Y');
			}

			return null;
		},
		'group' => true,
		'groupedRow' => true,
		'groupFooter' => function ($model, $key, $index, $widget) {
			return [
				'mergeColumns' => [[1, 3]],
				'content' => [
					4 => GridView::F_SUM,
					6 => GridView::F_SUM,
				],
				'contentFormats' => [
					4 => ['format' => 'number', 'decimals' => 2],
					6 => ['format' => 'number', 'decimals' => 2],
				],
				'contentOptions' => [
					4 => ['style' => 'text-align:right'],
					6 => ['style' => 'text-align:right'],
				],
			'options'=>['style'=>'font-weight:bold;']
			];
		}
	],
		[
		'label' => 'Time',
		'width' => '250px',
		'value' => function ($data) {
			return !empty($data->date) ? Yii::$app->formatter->asTime($data->date) : null;
		},
	],
		[
		'label' => 'Program',
		'width' => '250px',
		'value' => function ($data) {
			return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
		},
	],
		[
		'label' => 'Student',
		'value' => function ($data) {
			$student = ' - ';
			if($data->course->program->isPrivate()) {
				$student = !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
			}
			return $student;
		},
	],
		[
		'label' => 'Duration(hrs)',
		'value' => function ($data) {
			return $data->getDuration();
		},
		'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
	],
		[
		'label' => 'Rate/hour',
		'value' => function ($data) {
			return !empty($data->teacher->teacherRate->hourlyRate) ? $data->teacher->teacherRate->hourlyRate : null;
		},
		'hAlign'=>'right',
		'contentOptions' => ['class' => 'text-right'],
	],
		[
		'label' => 'Cost',
		'format'=>['decimal',2],
		'value' => function ($data) {
			$teacherRate = !empty($data->teacher->teacherRate->hourlyRate) ? $data->teacher->teacherRate->hourlyRate : null;
				return $data->getDuration() * $teacherRate;
		},
		'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
	],
];
} else {
	$columns = [
		[
			'label' => 'Date',
			'value' => function ($data) {
				if( ! empty($data->date)) {
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					return $lessonDate->format('l, F jS, Y');
				}

				return null;
			},
		],	
		[
			'label' => 'Duration(hrs)',
			'value' => function ($data){
				$locationId = Yii::$app->session->get('location_id');
				$lessons = Lesson::find()
					->location($locationId)
					->notDeleted()
					->andWhere(['DATE(date)' => (new \DateTime($data->date))->format('Y-m-d'), 'lesson.teacherId' => $data->teacherId])
					->all();
				$totalDuration = 0;
				foreach($lessons as $lesson) {
					$duration		 = \DateTime::createFromFormat('H:i:s', $lesson->duration);
					$hours			 = $duration->format('H');
					$minutes		 = $duration->format('i');
					$lessonDuration	 = $hours + ($minutes / 60);
					$totalDuration += $lessonDuration;	
				}
				return $totalDuration;
			},
			'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
		],
		[
			'label' => 'Cost',
		'format'=>['decimal',2],
		'value' => function ($data) {
				$locationId = Yii::$app->session->get('location_id');
				$lessons = Lesson::find()
					->location($locationId)
					->notDeleted()
					->andWhere(['DATE(date)' => (new \DateTime($data->date))->format('Y-m-d'), 'lesson.teacherId' => $data->teacherId])
					->all();
				$cost = 0;
				foreach($lessons as $lesson) {
					$duration		 = \DateTime::createFromFormat('H:i:s', $lesson->duration);
					$hours			 = $duration->format('H');
					$minutes		 = $duration->format('i');
					$lessonDuration	 = $hours + ($minutes / 60);
					$teacherRate = !empty($lesson->teacher->teacherRate->hourlyRate) ? $lesson->teacher->teacherRate->hourlyRate : null; 
					$cost += $lessonDuration * $teacherRate;	
				}
				return $cost;
		},
		'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
	],
	];
}
?>
<?=
GridView::widget([
	'dataProvider' => $teacherLessonDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-responsive'],
	'headerRowOptions' => ['class' => 'bg-light-gray-1'],
	'pjax' => true,
	'showPageSummary' => true,
	'pjaxSettings' => [
		'neverTimeout' => true,
		'options' => [
			'id' => 'teacher-lesson-grid',
		],
	],
	'columns' => $columns,
]);
?>
<script>
    $(document).ready(function () {
		$("#lessonsearch-summarisereport").on("change", function() {
        var summariesOnly = $(this).is(":checked");
        var fromDate = $('#lessonsearch-fromdate').val();
        var toDate = $('#lessonsearch-todate').val();
        var params = $.param({ 'LessonSearch[fromDate]': fromDate,
            'LessonSearch[toDate]': toDate, 'LessonSearch[summariseReport]': (summariesOnly | 0) });
        var url = '<?php echo Url::to(['user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $model->id]); ?>&' + params;
        $.pjax.reload({url:url,container:"#teacher-lesson-grid",replace:false,  timeout: 4000});  //Reload GridView
    });
        $("#teacher-lesson-search-form").on("submit", function () {
            var fromDate = $('#lessonsearch-fromdate').val();
            var toDate = $('#lessonsearch-todate').val();
            $.pjax.reload({container: "#teacher-lesson-grid", replace: false, timeout: 6000, data: $(this).serialize()});
            var url = "<?= Url::to(['user/print', 'id' => $model->id]); ?>&LessonSearch[fromDate]=" + fromDate + "&LessonSearch[toDate]=" + toDate;
            $('#print-btn').attr('href', url);
            return false;
        });
    });
</script>