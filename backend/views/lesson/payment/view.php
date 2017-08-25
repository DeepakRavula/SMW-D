<?php
use yii\helpers\Html;
use common\models\Payment;
use common\models\Invoice;
use common\models\PaymentMethod;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Url;
use yii\grid\GridView;

?>
<?php
$columns = [
    'date:date',
    'paymentMethod.name',
    [
        'label' => 'Number',
        'value' => function ($data) {
            if ($data->isCreditApplied() || $data->isCreditUsed()) {
                $invoice = Invoice::findOne(['id' => $data->reference]);
                $number = $invoice->getInvoiceNumber();
            } else {
                $number = $data->reference;
            }

            return $number;
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