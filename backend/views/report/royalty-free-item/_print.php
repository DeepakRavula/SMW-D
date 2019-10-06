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
<div class = "print-report">
<div>
    <h3><strong>Royalty Free Items </strong></h3>
    <?php if ($searchModel->fromDate === $searchModel->toDate): ?>
    <h3><?=  (new \DateTime($searchModel->toDate))->format('F jS, Y'); ?></h3>
    <?php else: ?>
    <h3><?=  (new \DateTime($searchModel->fromDate))->format('F jS, Y'); ?> to <?=  (new \DateTime($searchModel->toDate))->format('F jS, Y') ?></h3>
    <?php endif; ?></div>
<?php echo $this->render('_royaltyfree', ['model' => $searchModel, 'royaltyFreeDataProvider' => $royaltyFreeDataProvider,]); ?>
</div>

<script>
    $(document).ready(function () {
        setTimeout(function(){
            window.print();
}, 1500)
    });
</script>