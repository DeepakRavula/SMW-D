<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use common\models\Invoice;
use common\models\Payment;
use yii\bootstrap\ActiveForm;
?>


<?php
    $columns = [];

    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => function ($model) {
            return [
                'creditId' => $model['id'],
                'class' => 'text-left credit-type'
            ];
        },
        'label' => 'Type',
        'value' => 'type',
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Reference',
        'value' => 'reference',
    ]);


    array_push($columns, [
        'format' => 'currency',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right credit-value'],
        'label' => 'Amount',
        'value' => 'amount'
    ]);

?>

<?php Pjax::Begin(['id' => 'credit-lineitem-listing', 'timeout' => 6000]); ?>
    <?= GridView::widget([
        'id' => 'credit-line-item-grid',
        'dataProvider' => $creditDataProvider,
        'columns' => $columns,
        'summary' => false,
        'rowOptions' => ['class' => 'credit-items-value'],
        'emptyText' => 'No Credits Available!'
    ]); ?>
<?php Pjax::end(); ?>
