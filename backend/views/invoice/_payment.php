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

<?php $buttons = [];?>
<?php foreach(PaymentMethod::findAll([
			'active' => PaymentMethod::STATUS_ACTIVE,
			'displayed' => 1,
		]) as $method):?>
	<?php if((int) $model->type === Invoice::TYPE_PRO_FORMA_INVOICE):?>
	<?php if($method->name === 'Credit'):?>
	<?php continue;?>
	<?php endif;?>
	<?php endif;?>
	<?php $buttons[] = [
			'label' => $method->name, 
			'options' => [
				'class' => 'btn btn-default',
				'id' => str_replace(' ', '-', trim(strtolower($method->name))) . '-btn',
				'data-payment-type' => str_replace(' ', '-', trim(strtolower($method->name))),
				'data-payment-type-id' => $method->id,
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
			'id' => [4,5,6],
		]) as $method):?>
	<div id="<?= str_replace(' ', '-', trim(strtolower($method->name))) . '-section';?>" class="payment-method-section" style="display: none;">
		<?php echo $this->render('payment-method/_' . str_replace(' ', '-', trim(strtolower($method->name))),[
				'model' => new Payment(),
				'invoice' => $model,
		]);?>	
	</div>
	<?php endforeach;?>

<script type="text/javascript">
$(document).ready(function(){
  $('#payment-method-btn-section').on('click', '.btn', function() {
	 // debugger;
	 $('.payment-method-section').hide();
	 $('#' + $(this).data('payment-type') + '-section').show();
	 console.log($('#payment-payment_method_id'));
	 console.log($(this).data('payment-type-id'));
	 $('.payment-method-id').val($(this).data('payment-type-id'));
     if($(this).data('payment-type') == 'credit'){
         $('#credit-modal').modal('show');
     }
  });
  $('td').click(function () {
        var amount = $(this).closest('tr').data('amount');
        var id = $(this).closest('tr').data('id');
        var type = $(this).closest('tr').data('source');
        $('#payment-credit').val(amount);
		$('#payment-sourceid').val(id);
		$('#payment-sourcetype').val(type);
        $('#credit-modal').modal('hide');
    });
});
</script>