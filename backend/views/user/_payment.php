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
<div class="dn show-create-payment-form">
	<?php
	echo $this->render('_form-payment', [
		'model' => new Payment(),
	])
	?>
</div>
<center><b><h4 class="pull-left m-r-20 col-md-12"><?= 'Accounts Receivable Sub-Ledger for ' . $model->publicIdentity ?> </h4></b></center>
<?php yii\widgets\Pjax::begin() ?>
<?php
echo GridView::widget([
	'dataProvider' => $paymentDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'rowOptions' => function ($model, $key, $index, $grid) {
$u = \yii\helpers\StringHelper::basename(get_class($model));
$u = yii\helpers\Url::toRoute(['/' . strtolower($u) . '/view']);
return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="' . $u . '?id="+(this.id);'];
},
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
					case Allocation::TYPE_PAID:
						$description = 'Invoice Paid';
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
				if ($data->type === Allocation::TYPE_PAID) {
					return !empty($data->amount) ? $data->amount : null;
				}
			}
		],
		[
			'label' => 'Balance',
			'value' => function($data) {
				return !empty($data->balance->amount) ? Yii::$app->formatter->asCurrency($data->balance->amount) : null;
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
				->joinWith(['payment p' => function($query) use($model) {
						$query->where(['p.user_id' => $model->id]);
				}])
				->where(['allocation.type' => Allocation::TYPE_PAID])
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
		<td colspan="4">
		<td><?php echo ! empty($debitTotal) ? $debitTotal : null; ?></td>
		<td><?php echo ! empty($creditTotal) ? $creditTotal : null; ?></td>
		<td><?php echo ! empty($customerBalance) ? $customerBalance->amount : null; ?></td> 
		<td colspan="2">
		</tr>
	</table>
</div>