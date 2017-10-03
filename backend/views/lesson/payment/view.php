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
        'columns' => $columns,
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>	
</div>