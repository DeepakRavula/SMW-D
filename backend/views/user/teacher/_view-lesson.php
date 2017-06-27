<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Lesson;
use common\models\Qualification;
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
		.kv-page-summary, .table > tbody + tbody{
			border: 0;
		}
		.table-striped > tbody > tr:nth-of-type(odd){
			background: transparent;
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
		<div class="col-md-2 m-t-25">
			<?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['id' => 'print-btn', 'class' => 'btn btn-default btn-sm pull-right m-r-10', 'target' => '_blank']) ?>

		</div>
		<div class="clearfix"></div>
	</div>
</div>
<?php ActiveForm::end(); ?>

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
				],
				'contentFormats' => [
					4 => ['format' => 'number', 'decimals' => 2],
				],
				'contentOptions' => [
					4 => ['style' => 'text-align:right'],
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
];
?>
<?=
GridView::widget([
	'dataProvider' => $teacherLessonDataProvider,
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
        $("#teacher-lesson-search-form").on("submit", function () {
            var fromDate = $('#lessonsearch-fromdate').val();
            var toDate = $('#lessonsearch-todate').val();
            $.pjax.reload({container: "#teacher-lesson-grid", replace: false, timeout: 6000, data: $(this).serialize()});
			var params = $.param({ 'LessonSearch[fromDate]': fromDate,
            'LessonSearch[toDate]': toDate});
            var url = '<?= Url::to(['user/print', 'id' => $model->id]); ?>&' + params;
            $('#print-btn').attr('href', url);
            return false;
        });
    });
</script>