<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
use yii\widgets\Pjax;
?>
<?php
Pjax::begin([
    'id' => 'lesson-schedule'
]);
?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => '<i class="fa fa-pencil edit-enrolment-enddate"></i>',
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
	<dd><?= Yii::$app->formatter->asDate($model->course->endDate);?></dd>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>