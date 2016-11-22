<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use kartik\grid\GridView;
?>
<div>
  <?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['id' => 'print-btn', 'class' => 'btn btn-default btn-sm pull-right m-r-10', 'target' => '_blank']) ?>

	<?php $form = ActiveForm::begin([
	'id' => 'teacher-lesson-search-form',
]);
?>
	<div class="col-md-3">
		<?php
		echo $form->field($searchModel, 'fromDate')->widget(DatePicker::classname(),
			[
			'options' => [
				'class' => 'form-control',
			],
		])
		?>
    </div>
    <div class="col-md-3">
		<?php
		echo $form->field($searchModel, 'toDate')->widget(DatePicker::classname(),
			[
			'options' => [
				'class' => 'form-control',
			],
		])
		?>
    </div>
    <div class="col-md-3 form-group m-t-5">
        <br>
	<?php echo Html::submitButton(Yii::t('backend', 'Search'), ['id' => 'search', 'class' => 'btn btn-primary']) ?>
    </div>
	<?php ActiveForm::end(); ?>

	<?php
		$columns = [
			[
				'label' => 'Day',
				'value' => function ($data) {
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					$date = $lessonDate->format('l, F jS, Y');

					return !empty($date) ? $date : null;
				},
			],
			[
                'class' => 'kartik\grid\ExpandRowColumn',
                'width' => '50px',
				'enableRowClick' => true,
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_EXPANDED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('_teacher-lesson', ['model' => $model]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
            ]
		];
	?>
	<?= GridView::widget([
		'dataProvider' => $teacherLessonDataProvider,
		'options' => ['class' => 'col-md-12'],
		'footerRowOptions' => ['style' => 'font-weight:bold;text-align: left;'],
		'showFooter' => true,
		'tableOptions' => ['class' => 'table table-bordered'],
		'headerRowOptions' => ['class' => 'bg-light-gray'],
        'pjax' => true,
		'pjaxSettings' => [
			'neverTimeout' => true,
			'options' => [
				'id' => 'teacher-lesson-grid',
			],
		],
        'columns' => $columns,
        'showPageSummary' => true,
    ]); ?>
</div>
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