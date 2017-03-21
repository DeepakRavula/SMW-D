<?php

use yii\grid\GridView;
use common\models\Invoice;
use yii\data\ArrayDataProvider;

?>

<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
]) ?>
<div class="col-md-12 p-b-20">
<?php
echo GridView::widget([
'dataProvider' => $accountDataProvider,
'tableOptions' => ['class' => 'table table-bordered m-0'],
'headerRowOptions' => ['class' => 'bg-light-gray'],
'columns' => [
    [
        'headerOptions' => ['class' => 'text-left'],
        'contentOptions' => ['class' => 'text-left'],
        'label' => 'Description',
        'value'=> function ($data) {
            return $data->getAccountType() . ' #' . $data->foreignKeyId. ' '
                . $data->getAccountActionType();
        }
    ],
    [
        'headerOptions' => ['class' => 'text-right'],
        'format' => 'currency',
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Amount',
        'value'=> 'amount'
    ],
    [
        'headerOptions' => ['class' => 'text-right'],
        'format' => 'currency',
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Credit',
        'value' => function ($data) {
            return !empty($data->credit) ? $data->credit : 0;
        }
    ],
    [
        'headerOptions' => ['class' => 'text-right'],
        'format' => 'currency',
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Debit',
        'value' => function ($data) {
        return !empty($data->debit) ? $data->debit : 0;
        }
    ],
    [
        'headerOptions' => ['class' => 'text-right'],
        'format' => 'currency',
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Balance',
        'value' => 'balance'
    ],
    [
    'label' => 'Date',
    'value' => function ($data) {
        return (new \DateTime($data->date))->format('Y-m-d H:i:s');
    }
    ],
    [
    'label' => 'By User',
    'value' => function ($data) {
        return !empty($data->actionUser->publicIdentity) ?
            $data->actionUser->publicIdentity : null;
    }
    ]
],
]);
?>
</div>
<?php \yii\widgets\Pjax::end(); ?>