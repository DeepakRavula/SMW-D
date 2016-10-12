<?php

use yii\grid\GridView;
use common\models\Payment;
?>
<div>
	<?php
	echo $this->render('_view-opening-balance', [
		'openingBalanceDataProvider' => $openingBalanceDataProvider,
	])
	?>
</div>
<?php if( ! empty($openingBalanceCredit->id)):?>
<?php $paymentModel = Payment::find()
		->joinWith(['invoicePayment' => function($query) use($openingBalanceCredit){
			$query->where(['invoice_id' => $openingBalanceCredit->id]);
		}])
		->one();
?>
<div class="p-t-20 p-b-20">
	<div class="col-xs-2"><strong>Date:</strong> <?= Yii::$app->formatter->asDate($paymentModel->date);?></div>
	<div class="col-xs-3">
	<strong>Opening Balance:</strong>
	 <?= $openingBalanceCredit->balance;?>
	</div>
	<div class="clearfix"></div>
</div>
<?php elseif(! empty($positiveOpeningBalanceModel->id)):?>
<div class="p-t-20 p-b-20">
	<div class="col-xs-2"><strong>Date:</strong> <?= Yii::$app->formatter->asDate($positiveOpeningBalanceModel->date);?></div>
	<div class="col-xs-3">
	<strong>Opening Balance:</strong>
		<?= $positiveOpeningBalanceModel->total;?>
	</div>
	<div class="clearfix"></div>
</div>
<?php else:?>
	<?php
	echo $this->render('_form-opening-balance', [
		'model' => new Payment(),
		'userModel' => $model,
	])
	?>
<?php endif; ?>
