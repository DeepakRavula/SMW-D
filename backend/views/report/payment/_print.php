<?php

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

?>
<div>
<?php $reportText = 'Detail'; ?>
<?php if($searchModel->groupByMethod) : ?>
	<?php $reportText = 'Summary'; ?>
<?php endif; ?>
<h3><strong>Payments Received <?= $reportText; ?> Report </strong></h3></div>
<div><h3><?= $searchModel->fromDate->format('F jS, Y') . ' to ' . $searchModel->toDate->format('F jS, Y');?></h3></div>
<?php echo $this->render('_payment', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>

<script>
	$(document).ready(function(){
		window.print();
	});
</script>