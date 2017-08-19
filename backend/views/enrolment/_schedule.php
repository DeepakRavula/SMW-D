<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => 'Schedule',
])
?>
<div class="col-xs-3 p-0"><strong>Day</strong></div>
<div class="col-xs-6">
	<?php $dayList = Course::getWeekdaysList();
    echo $dayList[$model->courseSchedule->day]; ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-3 p-0"><strong>Time</strong></div>
<div class="col-xs-6">
	<?= Yii::$app->formatter->asTime($model->courseSchedule->fromTime);?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-3 p-0"><strong>Start Date</strong></div>
<div class="col-xs-6">
	<?= Yii::$app->formatter->asDate($model->course->startDate);?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-3 p-0"><strong>End Date</strong></div>
<div class="col-xs-6">
	<?= Yii::$app->formatter->asDate($model->course->endDate);?>
</div> 
<div class='clearfix'></div>
<?php
LteBox::end()?>