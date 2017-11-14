<?php

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
use common\models\Location;
?>
<div>
<?php $reportText = 'Summary'; ?>
<?php $model = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
<?php
   echo $this->render('/print/_print-header', [
       'locationModel'=>$model,
]);
   ?>
<div><h3><?= (new \DateTime($searchModel->fromDate))->format('F jS, Y') . ' to ' . (new \DateTime($searchModel->toDate))->format('F jS, Y');?></h3></div>
<?php echo $this->render('_item-category', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>

<script>
	$(document).ready(function(){
		window.print();
	});
</script>