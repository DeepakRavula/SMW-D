<?php
use yii\grid\GridView;
use common\models\Payment;
use common\models\Allocation;
use common\models\Invoice;
?>
<?php yii\widgets\Pjax::begin() ?>
<?php echo GridView::widget([
        'dataProvider' => $invoicePayments,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' =>['class' => 'table table-bordered'],
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
                    return ! empty($data->payment->paymentMethod->name) ? $data->payment->paymentMethod->name : null;
                },
            ],
			'amount',
	    ],
    ]); ?>
<?php \yii\widgets\Pjax::end(); ?>
<?php
	$payment = Payment::find()
		->joinWith(['allocation a' => function($query){
			$query->where(['a.invoice_id' => Allocation::TYPE_OPENING_BALANCE]);
		}])
		->where(['user_id' => $model->user_id])
		->one();
	$openingBalance = $payment->amount;
	$proformaPayments = Allocation::find()
		->joinWith(['invoice i' => function($query) use($model){
			$query->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
		}])
		->joinWith(['payment p' => function($query) use($model){
			$query->where(['p.user_id' => $model->user_id]);
		}])
		->all();
		$proformaAmount = 0;
		foreach($proformaPayments as $proformaPayment){
			$proformaAmount += $proformaPayment['amount'];	
		}

	$invoicePayments = Allocation::find()
		->joinWith(['invoice i' => function($query) use($model){
			$query->where(['i.type' => Invoice::TYPE_INVOICE]);
		}])
		->joinWith(['payment p' => function($query) use($model){
			$query->where(['p.user_id' => $model->user_id]);
		}])
		->all();
		$invoiceAmount = 0;
		foreach($invoicePayments as $invoicePayment){
			$invoiceAmount += $invoicePayment['amount'];	
		}
		$balance = ($openingBalance + $proformaAmount) - $invoiceAmount;
echo $model->lineItems[0]->lesson->enrolment->student->customer->publicIdentity . ' Balance: ' . $balance; 
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Payments </h4> 
	<a href="#" class="add-new-payment text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<div class="dn show-create-payment-form">
	<?php echo $this->render('_form-payment', [
		'model' => new Payment(),
	]) ?>
</div>
