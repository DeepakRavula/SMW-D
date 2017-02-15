<?php

use yii\widgets\ListView;
?>
<div>
<h3 class="m-0"> <?= $model->publicIdentity; ?> </h3>
<h4> <?= $fromDate->format('M jS, Y') . ' to ' . $toDate->format('M jS, Y'); ?> </h4>
<?php echo ListView::widget([
		'dataProvider' => $teacherLessonDataProvider,
		'itemView' => '_print-content',
	]); ?>
	
</div>
<div>
<?php
    $lessonCount = $teacherAllLessonDataProvider->totalCount;
    $totalDuration     = 0;
	$lessonTotal = 0;
	$totalCost = 0;
    if (!empty($teacherAllLessonDataProvider->getModels())) {
        foreach ($teacherAllLessonDataProvider->getModels() as $key => $val) {
            $duration         = \DateTime::createFromFormat('H:i:s', $val->duration);
            $hours             = $duration->format('H');
            $minutes         = $duration->format('i');
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
    ?>
    <div class="row">
    	<div class="col-md-10">
            <strong><?= 'Total Hours of Instruction' . ' : ' . $totalDuration . 'hrs'; ?></strong>
        </div>
		<div class="col-md-10">
            <strong><?= 'Total Cost' . ' : $' . $totalCost; ?></strong>
        </div>
    	<div class="clearfix"></div>
        <div class="col-md-10">
        	<strong><?= $lessonCount . ' Lessons in total'; ?></strong>
    	</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>