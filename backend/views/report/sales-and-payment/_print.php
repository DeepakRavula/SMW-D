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
    <?php if ($searchModel->fromDate === $searchModel->toDate): ?>
    <h2> for <?=  Yii::$app->formatter->asDate($searchModel->toDate) ?></h2>
    <?php else: ?>
    <h2>for <?=  Yii::$app->formatter->asDate($searchModel->fromDate) ?> to <?=  Yii::$app->formatter->asDate($searchModel->toDate) ?></h2>
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