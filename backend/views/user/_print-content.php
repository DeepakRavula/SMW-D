<?php
use yii\helpers\Html;

?>
<div class="address p-b-10 relative">
    <div class="col-md-4 p-0"><h4><strong>
		<?php
		$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
		$date = $lessonDate->format('l, F jS, Y');
		echo Html::encode(!empty($date) ? $date : null) ?></strong></h4></div>
    <div class="clearfix"></div>
	<div class="">
	<?=
	$this->render('_teacher-lesson', [
		'model' => $model,
    ]);
	?>
	</div>
</div>