<?php

use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Reports';
?>
<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$paymentContent = $this->render('_index-payment', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
]);

?>

<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Payments',
            'content' => $paymentContent,
        ],
    ],
]); ?>
