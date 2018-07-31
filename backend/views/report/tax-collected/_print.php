<?php
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

use common\models\Location;

?>
<?php $model = Location::findOne(['id' => Location::findOne(['slug' => \Yii::$app->location])->id]); ?>
<?php
   echo $this->render('/print/_header', [
       'locationModel' => $model,
]);
   ?>
<div>
    <h3><strong>Tax Collected Report </strong></h3></div>
<div><h3><?= (new \DateTime($searchModel->fromDate))->format('F jS, Y') . ' to ' . (new \DateTime($searchModel->toDate))->format('F jS, Y'); ?></h3></div>
<?php echo $this->render('_taxcollected', [
    'searchModel' => $searchModel, 
    'taxDataProvider' => $taxDataProvider,
    'taxSum' => $taxSum,
    'subtotalSum' => $subtotalSum,
    'totalSum' => $totalSum
]); ?>

<script>
    $(document).ready(function () {
        window.print();
    });
</script>