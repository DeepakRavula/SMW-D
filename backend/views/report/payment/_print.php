<?php

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

?>
<h3>Payments<center>Date : <?= $searchModel->fromDate->format('d-m-Y') . ' to ' . $searchModel->toDate->format('d-m-Y');?></center></h3>

<?php echo $this->render('_payment', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>

<script>
	$(document).ready(function(){
		window.print();
	});
</script>