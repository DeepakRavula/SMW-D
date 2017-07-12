<?php

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

?>
<div>
<?php $reportText = 'Summary'; ?>

<h3><strong>Item Category <?= $reportText; ?> Report </strong></h3></div>
<div><h3><?= (new \DateTime($searchModel->fromDate))->format('F jS, Y') . ' to ' . (new \DateTime($searchModel->toDate))->format('F jS, Y');?></h3></div>
<?php echo $this->render('_item-category', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>

<script>
	$(document).ready(function(){
		window.print();
	});
</script>