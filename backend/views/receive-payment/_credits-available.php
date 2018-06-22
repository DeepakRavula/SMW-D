<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

?>

<?php
    $columns = [];
    array_push($columns, [
        'class' => 'yii\grid\CheckboxColumn',
        'contentOptions' => ['style' => 'width:30px;'],
        'checkboxOptions' => function($model, $key, $index, $column) {
            return ['checked' => true, 'class' =>'check-checkbox'];
        }
    ]);

    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left credit-type'],
        'label' => 'Type',
        'value' => 'type',
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

