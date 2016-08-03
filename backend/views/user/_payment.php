<?php

use yii\grid\GridView;
use common\models\Payment;
use common\models\Allocation;
use common\models\BalanceLog;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Opening Balance </h4> 
	<a href="#" class="add-new-payment text-add-new"><i class="fa fa-plus"></i></a>
	<div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<hr class="hr-ad right-side-faded hr-payment">
<div class="dn show-create-payment-form">
	<?php
	echo $this->render('_form-payment', [
		'model' => new Payment(),
	])
	?>
</div>
<?php yii\widgets\Pjax::begin() ?>
<?php
echo GridView::widget([
	'dataProvider' => $paymentDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
	'columns' => [
		[
			'label' => 'Date',
			'value' => function($data) {
				$date = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
				return !empty($data->date) ? $date->format('d M Y') : null;
			},
		],
		[
			'label' => 'Description',
			'value' => function($data) {
				switch ($data->type) {
					case Allocation::TYPE_OPENING_BALANCE:
						$description = 'Opening Balance';
						break;
					case Allocation::TYPE_RECEIVABLE:
						$description = 'Payment Received';
						break;
					case ($data->type == Allocation::TYPE_PAID) && ($data->invoice_id != Payment::TYPE_CREDIT) && ($data->payment_id != Payment::TYPE_CREDIT):
						$description = 'Invoice Paid';
						break;
					case Allocation::TYPE_CREDIT_USED:
						$description = 'Credit Used';
						break;
					default:
						$description = null;
				}
				return $description;
			}
		],
		[
			'label' => 'Debit',
			'value' => function($data) {
				if ($data->type === Allocation::TYPE_OPENING_BALANCE || $data->type === Allocation::TYPE_RECEIVABLE) {
					return !empty($data->amount) ? $data->amount : null;
				}
			}
		],
		[
			'label' => 'Credit',
			'value' => function($data) {
				if ($data->type === Allocation::TYPE_PAID || $data->type === Allocation::TYPE_CREDIT_USED) {
					return !empty($data->amount) ? $data->amount : null;
				}
			}
		],
		[
			'label' => 'Balance',
			'value' => function($data) {
				return !empty($data->balance->amount) ? $data->balance->amount : null;
			}
		],
	],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<?php
$debitTypes = [Allocation::TYPE_OPENING_BALANCE, Allocation::TYPE_RECEIVABLE];
$customerDebits = Allocation::find()
		->joinWith(['payment p' => function($query) use($model) {
				$query->where(['p.user_id' => $model->id]);
			}])
		->where(['in', 'allocation.type', $debitTypes])
		->all();
		$debitTotal = 0;
		if (!empty($customerDebits)) {
			foreach ($customerDebits as $customerDebit) {
				$debitTotal += $customerDebit['amount'];
			}
		}

		$customerCredits = Allocation::find()
				->joinWith('payment p')
				->where(['p.user_id' => $model->id])
				->joinWith('invoice i')
				->orWhere(['i.user_id' => $model->id])
				->andWhere(['allocation.type' => Allocation::TYPE_PAID])
				->all();
				$creditTotal = 0;
				if (!empty($customerCredits)) {
					foreach ($customerCredits as $customerCredit) {
						$creditTotal += $customerCredit['amount'];
					}
				}

		$customerBalance = BalanceLog::find()
						->orderBy(['id' => SORT_DESC])
						->where(['user_id' => $model->id])->one();
?>
<div>
	<table class="table-invoice-childtable">
		<tr>
			<?php if(! empty($debitTotal)):?>
			<td><?php echo 'Debit Total: '; ?></td>
			<td><?php echo $debitTotal; ?></td>
			<?php endif;?>
		</tr>
		<tr>
			<?php if(! empty($creditTotal)):?>
			<td><?php echo 'Credit Total: '; ?></td>
			<td><?php echo $creditTotal; ?></td>
			<?php endif;?>
		</tr>
		<tr>
			<?php if(! empty($customerBalance)):?>
			<td><?php echo 'Balance Total: '; ?></td>
			<td><?php echo $customerBalance->amount; ?></td>
			<?php endif;?>
		</tr>
	</table>
</div>