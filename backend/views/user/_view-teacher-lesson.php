<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
?>
<div>
  <?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default btn-sm pull-right m-r-10', 'target' => '_blank']) ?>
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
	yii\widgets\Pjax::begin([
		'id' => 'teacher-lesson-grid',
	])
	?>
	<?php
	echo GridView::widget([
		'id' => 'teacher-lesson',
		'dataProvider' => $teacherLessonDataProvider,
		'options' => ['class' => 'col-md-12'],
		'footerRowOptions' => ['style' => 'font-weight:bold;text-align: left;'],
		'showFooter' => true,
		'tableOptions' => ['class' => 'table table-bordered'],
		'headerRowOptions' => ['class' => 'bg-light-gray'],
		'columns' => [
			[
				'label' => 'Day',
				'value' => function ($data) {
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					$date = $lessonDate->format('l, F jS, Y');

					return !empty($date) ? $date : null;
				},
			],
		],
	]);
	?>
	<?php \yii\widgets\Pjax::end(); ?>
</div>
<script>
$(document).ready(function(){
	$('#teacher-lesson-search-form').submit(function(){
		var url = "<?= Url::to(['user/lesson-search', 'id' => $model->id]); ?>";
		$.pjax.reload({url:url, container:"#teacher-lesson-grid", replace: false, timeout : 6000, data : $(this).serialize()});  //Reload GridView
		 return false;
	});
});
</script>