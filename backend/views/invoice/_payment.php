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
					if($data->payment_id == Payment::TYPE_CREDIT){
						return 'Credit Applied';
					}else{
                    return ! empty($data->payment->paymentMethod->name) ? $data->payment->paymentMethod->name : null;
					}
                },
            ],
			'amount',
	    ],
    ]); ?>
<?php \yii\widgets\Pjax::end(); ?>

<div class="col-md-12 m-b-20">
	<a href="#" class="add-new-payment text-add-new"><i class="fa fa-plus-circle"></i> Add Payment method</a>
	<div class="clearfix"></div>
</div>
<div class="dn show-create-payment-form">
	<?php echo $this->render('_form-payment', [
		'model' => new Payment(),
		'invoiceModel' => $model,
	]) ?>
</div>
