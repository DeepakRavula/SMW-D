<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Items';
?>

<div class="payments-index p-10">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo $this->render('_item', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]); ?>

</div>
