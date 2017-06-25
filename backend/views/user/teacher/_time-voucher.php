<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Lesson;
use common\models\Qualification;
?>
<style>
	#w20-container table > tbody > tr.info > td{
		padding:8px;
		background:#fff;
	}
	.kv-page-summary, .table > tbody + tbody{
		border: 0;
	}
	.table-striped > tbody > tr:nth-of-type(odd){
		background: transparent;
	}
</style>

<?php
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
		'format'=>['decimal',2],
		'value' => function ($data) {
			$qualification = Qualification::findone(['teacher_id' => $data->teacherId, 'program_id' => $data->course->program->id]); 
			$rate = !empty($qualification->rate) ? $qualification->rate : 0;
			return $rate;
		},
		'hAlign'=>'right',
		'contentOptions' => ['class' => 'text-right'],
	],
		[
		'label' => 'Cost',
		'format'=>['decimal',2],
		'value' => function ($data) {
			$qualification = Qualification::findone(['teacher_id' => $data->teacherId, 'program_id' => $data->course->program->id]);
			$rate = !empty($qualification->rate) ? $qualification->rate : 0;
			return $data->getDuration() * $rate;
		},
		'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
	],
];
?>
<?=
GridView::widget([
	'dataProvider' => $timeVoucherDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
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
		var printUrl = '<?= Url::to(['user/print', 'id' => $model->id]); ?>&' + params;
		 $('#print-btn').attr('href', printUrl);
    });
        $("#teacher-lesson-search-form").on("submit", function () {
        	var summariesOnly = $("#lessonsearch-summarisereport").is(":checked");
            var fromDate = $('#lessonsearch-fromdate').val();
            var toDate = $('#lessonsearch-todate').val();
            $.pjax.reload({container: "#teacher-lesson-grid", replace: false, timeout: 6000, data: $(this).serialize()});
			var params = $.param({ 'LessonSearch[fromDate]': fromDate,
            'LessonSearch[toDate]': toDate, 'LessonSearch[summariseReport]': (summariesOnly | 0) });
            var url = '<?= Url::to(['user/print', 'id' => $model->id]); ?>&' + params;
            $('#print-btn').attr('href', url);
            return false;
        });
    });
</script>