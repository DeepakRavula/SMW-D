<?php

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

?>
<div>
<?php $reportText = 'Student Birthdays'; ?>
<h3><strong><?= $reportText; ?> Report </strong></h3></div>
<div><h3><?= $searchModel->fromDate->format('F jS') . ' to ' . $searchModel->toDate->format('F jS');?></h3></div>
<?php echo $this->render('_birthday', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>

<script>
	$(document).ready(function(){
		window.print();
	});
</script>