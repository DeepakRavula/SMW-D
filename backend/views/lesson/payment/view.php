<?php
use common\models\Invoice;
use yii\grid\GridView;

?>
<?php
$columns = [
    'date:date',
    'paymentMethod.name',
    [
        'label' => 'Number',
        'value' => function ($data) {
            return $data->reference;
        },
    ],
    [
        'attribute' => 'amount',
        'format' => 'currency',
        'value' => function ($data) {
            return Yii::$app->formatter->asDecimal($data->amount);
        },
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right']
    ],
]; ?>
<div>
	<?php yii\widgets\Pjax::begin([
        'id' => 'invoice-payment-listing',
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
</div>