<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\grid\GridView;
use yii\widgets\Pjax;

?>

<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
   
    'title' => 'Payments',
    'withBorder' => true,
])
?>
<?php
$columns = [
    [
        'label' => 'Date',
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data->payment->date);
        },
    ],
    [
        'label' => 'Payment Method',
        'value' => function ($data) {
            return $data->payment->paymentMethod->name;
        },
    ],
    [
        'label' => 'Number',
        'value' => function ($data) {
            return $data->payment->reference;
        },
    ],
    [
        'attribute' => 'amount',
        'format' => 'currency',
        'value' => function ($data) {
            return round($data->amount, 2);
        },
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right']
    ],
]; ?>
<div>
<?php Pjax::begin([
    'id' => 'lesson-payment-listing',
    'timeout' => 6000,
]) ?>
    <?= GridView::widget([
        'id' => 'payment-grid',
        'dataProvider' => $paymentsDataProvider,
        'summary' => false,
        'emptyText' => false,
        'columns' => $columns,
    ]);
    ?>
    <?php \yii\widgets\Pjax::end(); ?>	
    <?php LteBox::end() ?>

</div>