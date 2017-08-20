<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
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