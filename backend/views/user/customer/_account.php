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
    'label' => 'Date',
    'value' => function ($data) {
        return Yii::$app->formatter->asDate($data->date);
    }
    ],
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
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Credit ($)',
        'value' => function ($data) {
            return !empty($data->credit) ? $data->credit : 0;
        }
    ],
    [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Debit ($)',
        'value' => function ($data) {
        return !empty($data->debit) ? $data->debit : 0;
        }
    ],
    [
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right'],
        'label' => 'Balance ($)',
        'value' => 'balance'
    ],
   
],
]);
?>
</div>
<?php \yii\widgets\Pjax::end(); ?>