<?php

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
use common\models\Location;

?>
<?php $model = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
<?php
   echo $this->render('/print/_header', [
       'locationModel'=>$model,
]);
   ?>
<div>
<h3><strong>Discount Report </strong></h3></div>
<div><h3><?= $searchModel->fromDate->format('F jS, Y') . ' to ' . $searchModel->toDate->format('F jS, Y');?></h3></div>
<?php echo $this->render('_discount', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>

<script>
    $(document).ready(function(){
        window.print();
    });
</script>