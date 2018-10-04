<?php
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

use common\models\Location;

?>
<?php $model = Location::findOne(['id' => \common\models\Location::findOne(['slug' => \Yii::$app->location])->id]); ?>
<?php
   echo $this->render('/print/_header', [
       'locationModel'=>$model,
]);
   ?>
<div>
    <h1><strong>Sales and Payment Report </strong></h1>
    <h2>for <?= $searchModel->fromDate ?> to <?= $searchModel->toDate ?></h2>
    </div>
    <?php
    echo $this->render('_sales', [
    'searchModel' => $searchModel, 
    'salesDataProvider' => $salesDataProvider,
]);
    echo $this->render('_payment', [
    'searchModel' => $searchModel, 
    'paymentsDataProvider' => $paymentsDataProvider,
]);


?>
<script>
    $(document).ready(function () {
        window.print();
    });
</script>