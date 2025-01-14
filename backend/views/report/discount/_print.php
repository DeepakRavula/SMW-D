<?php

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
use common\models\Location;

?>
<?php $model = Location::findOne(['id' => \common\models\Location::findOne(['slug' => \Yii::$app->location])->id]); ?>
<div class = "print-report">
<div>
<h3><strong>Discount Report </strong></h3></div>
<div><?php if ($searchModel->fromDate === $searchModel->toDate): ?>
    <h3><?= $searchModel->toDate->format('F jS, Y'); ?></h3>
    <?php else: ?>
    <h3><?= $searchModel->fromDate->format('F jS, Y'); ?> to <?= $searchModel->toDate->format('F jS, Y') ?></h3>
    <?php endif; ?></div>
<?php echo $this->render('_discount', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>
</div>

<script>
    $(document).ready(function(){
        setTimeout(function(){
            window.print();
}, 1500)
    });
</script>