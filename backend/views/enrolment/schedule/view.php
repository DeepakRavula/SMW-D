<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\daterange\DateRangePickerAsset;

DateRangePickerAsset::register($this);
?>
<?php $boxTools = $this->render('_box-tool', [
    'model' => $model,
]);?>
<?php
Pjax::begin([
    'id' => 'lesson-schedule'
]);
?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => $boxTools,
    'title' => 'Schedule',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Day</dt>
	<dd><?php $dayList = Course::getWeekdaysList();
    echo $dayList[$model->courseSchedule->day]; ?></dd>
	<dt>Time</dt>
	<dd><?= Yii::$app->formatter->asTime($model->courseSchedule->fromTime);?></dd>
	<dt>Start Date</dt>
	<dd><?= Yii::$app->formatter->asDate($model->course->startDate);?></dd>
	<dt>End Date</dt>
	<?php if($model->course->program->isGroup()) : ?>
	<dd><?= Yii::$app->formatter->asDate($model->endDateTime);?></dd>
	<?php else : ?>
	<dd><?= Yii::$app->formatter->asDate($model->course->endDate);?></dd>
	<?php endif;?>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>


<script>
$(document).ready(function (e) {
	$(document).on('click', '.enrolment-edit-cancel', function() {
		$('#enrolment-edit-modal').modal('hide');
		return false;
	});
	
});
</script>