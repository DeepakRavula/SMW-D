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
    <h3><strong>Sales and Payments Report </strong></h3>
    <?php if ($searchModel->fromDate === $searchModel->toDate): ?>
    <h3><?=  (new \DateTime($searchModel->toDate))->format('F jS, Y'); ?></h3>
    <?php else: ?>
    <h3><?=  (new \DateTime($searchModel->fromDate))->format('F jS, Y'); ?> to <?=  (new \DateTime($searchModel->toDate))->format('F jS, Y') ?></h3>
    <?php endif; ?>
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