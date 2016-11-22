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
<script>
	$(document).ready(function(){
		window.print();
	});
</script>