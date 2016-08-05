<?php
use yii\grid\GridView;
use common\models\Payment;
use common\models\Allocation;
use common\models\Invoice;
use common\models\BalanceLog;
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
					if($data->payment_id == Payment::TYPE_CREDIT && $data->type == Allocation::TYPE_CREDIT_APPLIED){
						return 'Credit Applied';
					}
					elseif($data->payment_id == Payment::TYPE_CREDIT && $data->type == Allocation::TYPE_CREDIT_USED){
						return 'Credit Used';
					}else{
                    return ! empty($data->payment->paymentMethod->name) ? $data->payment->paymentMethod->name : null;
					}
                },
            ],
			[
                'label' => 'Amount',
                'value' => function($data) {
					if($data->type === Allocation::TYPE_PAID || $data->type === Allocation::TYPE_CREDIT_APPLIED){
                    	return ! empty($data->amount) ? $data->amount : null;
					}else{
	                    return ! empty($data->balance->amount) ? $data->balance->amount : null;
					}
                },
            ],
	    ],
    ]); ?>
<?php \yii\widgets\Pjax::end(); ?>


	<?php
	$customerBalance = BalanceLog::find()
			->orderBy(['id' => SORT_DESC])
			->where(['user_id' => $model->user_id])->one();
	?>
<div>
	Customer Name: <?=$model->user->publicIdentity;?>
</div>
<div>
	Customer Credits Available: <?= ! empty($customerBalance->amount) ? $customerBalance->amount : '0';?>
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
<div class="col-md-12 m-b-20">
	<a href="#" class="add-new-payment text-add-new"><i class="fa fa-plus-circle"></i> Add Payment</a>
	<div class="clearfix"></div>
</div>
<div class="dn show-create-payment-form">
	<?php echo $this->render('_form-payment', [
		'model' => new Payment(),
	]) ?>
</div>
