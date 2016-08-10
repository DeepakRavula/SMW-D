<?php
use yii\grid\GridView;
use common\models\Payment;
use common\models\Allocation;
use common\models\Invoice;
use common\models\BalanceLog;
use yii\data\ActiveDataProvider;
use common\models\PaymentMethod;
use yii\widgets\ListView;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Button;
?>
<?php yii\widgets\Pjax::begin() ?>
<?php echo GridView::widget([
        'dataProvider' => $invoicePayments,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' =>['class' => 'table table-bordered m-0'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
		'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'columns' => [
            [
                'label' => 'Date',
                'value' => function($data) {
					$date = \DateTime::createFromFormat('Y-m-d H:i:s',$data->date);
                    return ! empty($data->date) ? $date->format('d M Y') : null;
                },
            ],
			[
                'label' => 'Payment Method',
                'value' => function($data) {
                    return ! empty($data->paymentMethod->name) ? $data->paymentMethod->name : null;
				}
            ],
			[
                'label' => 'Amount',
                'value' => function($data) {
                    	return ! empty($data->amount) ? abs($data->amount) : null;
                },
            ],
	    ],
    ]); ?>
<?php \yii\widgets\Pjax::end(); ?>

<div>
	Customer Name: <?=$model->user->publicIdentity;?>
</div>
<div>
	Customer Credits Available: <?= ! empty($model->getCustomerBalance($model->user_id)) ? $model->getCustomerBalance($model->user_id) : '0';?>
</div>
<div>
	Invoice Total: <?= $model->total;?>
</div>
<div>
	Invoice Paid: <?= $model->invoicePaymentTotal;?>
</div>
<div>
	Invoice Balance: <?= $model->invoiceBalance;?>
</div>

<?php $buttons = [];?>
<?php foreach(PaymentMethod::findAll([
			'active' => PaymentMethod::STATUS_ACTIVE,
			'displayed' => 1,
		]) as $method):?>
	<?php $buttons[] = [
			'label' => $method->name, 
			'options' => [
				'class' => 'btn btn-default',
				'id' => str_replace(' ', '-', trim(strtolower($method->name))) . '-btn',
				'data-payment-type' => str_replace(' ', '-', trim(strtolower($method->name))),
			],
	];?>
<?php endforeach;?>

<?php // a button group with items configuration
echo ButtonGroup::widget([
    'buttons' => $buttons,
	'options' => [
		'id' => 'payment-method-btn-section',
		'class' => 'btn-group-vertical'
	]
]);?>

<?php foreach(PaymentMethod::findAll([
			'active' => PaymentMethod::STATUS_ACTIVE,
			'displayed' => 1,
			'id' => [4,5],
		]) as $method):?>
	<div id="<?= str_replace(' ', '-', trim(strtolower($method->name))) . '-section';?>" class="payment-method-section" style="display: none;">
		<?php echo $this->render('payment-methods/_' . str_replace(' ', '-', trim(strtolower($method->name))),[
				'model' => new Payment(),
		]);?>	
	</div>
	<?php endforeach;?>

<div class="col-md-12 m-b-20">
	<a href="#" class="add-new-payment text-add-new"><i class="fa fa-plus-circle"></i> Add Payment</a>
	<div class="clearfix"></div>
</div>
<div class="dn show-create-payment-form">
	<?php echo $this->render('_form-payment', [
		'model' => new Payment(),
	]) ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
  $('#payment-method-btn-section').on('click', '.btn', function() {
	 $('.payment-method-section').hide();
	 $('#' + $(this).data('payment-type') + '-section').show();
  });
});
</script>