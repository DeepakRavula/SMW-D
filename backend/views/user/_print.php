<?php

use yii\widgets\ListView;
?>
<div>
<h3> <?= $model->publicIdentity; ?> </h3>
<?php echo ListView::widget([
		'dataProvider' => $teacherLessonDataProvider,
		'itemView' => '_print-content',
	]); ?>
	
</div>
<div>
<?php
    $lessonCount = $teacherAllLessonDataProvider->totalCount;
    $totalDuration     = 0;
    if (!empty($teacherAllLessonDataProvider->getModels())) {
        foreach ($teacherAllLessonDataProvider->getModels() as $key => $val) {
            $duration         = \DateTime::createFromFormat('H:i:s', $val->duration);
            $hours             = $duration->format('H');
            $minutes         = $duration->format('i');
            $lessonDuration     = ($hours * 60) + $minutes;
            $totalDuration += $lessonDuration;
        }
    }
    ?>
    <div class="col-md-2 p-0">
    	<strong><?= $lessonCount . ' Lessons in total'; ?></strong>
	</div>
	<div class="col-md-10">
    <strong><?= $totalDuration . 'm'; ?></strong>
    </div>
</div>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>