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
<?php $form = ActiveForm::begin([
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
			echo $form->field($searchModel, 'fromDate')->widget(DatePicker::classname(),
				[
				'options' => [
					'class' => 'form-control',
				],
			])
			?>
	    </div>
	    <div class="col-md-2">
			<?php
			echo $form->field($searchModel, 'toDate')->widget(DatePicker::classname(),
				[
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
	    <div class="col-md-6 m-t-25">
	    	<?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['id' => 'print-btn', 'class' => 'btn btn-default btn-sm pull-right m-r-10', 'target' => '_blank']) ?>

	    </div>
	    <div class="clearfix"></div>
	</div>
</div>
	<?php ActiveForm::end(); ?>

	<?php
		$lessonCount = $teacherAllLessonDataProvider->totalCount;
		$totalDuration	 = 0;
		$lessonTotal = 0;
		$totalCost = 0;
		if (!empty($teacherAllLessonDataProvider->getModels())) {
			foreach ($teacherAllLessonDataProvider->getModels() as $key => $val) {
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
		$columns = [
			[
				'label' => 'Time',
				'width'=>'250px',
            	'value' => function ($data) {
					return !empty($data->date) ? Yii::$app->formatter->asTime($data->date) : null;
				},
				 'group'=>true,  // enable grouping,
            'groupedRow'=>true,                    // move grouped column to a single grouped row
            'groupOddCssClass'=>'kv-grouped-row',  // configure odd group cell css class
            'groupEvenCssClass'=>'kv-grouped-row',
			],
			[
				'label' => 'Program',
				'width'=>'250px',
				'value' => function ($data) {
					return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
				},
			'group'=>true,  // enable grouping
            'subGroupOf'=>1
			],
			[
				'label' => 'Student',
				'value' => function ($data) {
					return !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
				},
			],
			[
				'label' => 'Duration(hrs)',
				'value' => function ($data) {
					return $data->getDuration();
				},
			],
			[
				'label' => 'Rate',
				'value' => function ($data) {
					return $data->course->program->rate;	
				},
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
			],
		];
	?>
	<?= GridView::widget([
		'dataProvider' => $teacherLessonDataProvider,
		'options' => ['class' => 'col-md-12'],
		'tableOptions' => ['class' => 'table table-bordered table-responsive'],
		'headerRowOptions' => ['class' => 'bg-light-gray-1'],
        'pjax' => true,
		'showPageSummary'=>true,
		'pjaxSettings' => [
			'neverTimeout' => true,
			'options' => [
				'id' => 'teacher-lesson-grid',
			],
		],
        'columns' => $columns,
    ]); ?>
<script>
$(document).ready(function(){
	$("#teacher-lesson-search-form").on("submit", function() {
		var fromDate = $('#lessonsearch-fromdate').val();
		var toDate = $('#lessonsearch-todate').val();
		$.pjax.reload({container:"#teacher-lesson-grid", replace:false, timeout:6000, data:$(this).serialize()});
		var url = "<?= Url::to(['user/print', 'id' => $model->id]); ?>&LessonSearch[fromDate]=" + fromDate + "&LessonSearch[toDate]=" + toDate;
		$('#print-btn').attr('href', url);
		return false;
    });
});
</script>