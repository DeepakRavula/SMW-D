<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reports';
?>

<?php echo $this->render('_index-payment', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]); ?>


