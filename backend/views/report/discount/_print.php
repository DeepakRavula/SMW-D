<?php

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

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