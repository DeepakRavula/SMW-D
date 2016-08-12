<?php

use yii\grid\GridView;
use common\models\Payment;
use common\models\Allocation;
use common\models\BalanceLog;
?>
<div>
	<?php
	echo $this->render('_view-opening-balance', [
		'openingBalanceDataProvider' => $openingBalanceDataProvider,
	])
	?>
</div>
<?php if( ! empty($openingBalancePaymentModel->id)):?>
<div>
Opening Balance:
<?php if($openingBalancePaymentModel->amount > 0) :?>
	<?= -abs($openingBalancePaymentModel->amount);?>
<?php else:?>
 <?= abs($openingBalancePaymentModel->amount);?>
<?php endif;?>
</div>
<div>
Date: <?= Yii::$app->formatter->asDate($openingBalancePaymentModel->date);?>
</div>
<?php else:?>
<div>
	<?php
	echo $this->render('_form-payment', [
		'model' => new Payment(),
	])
	?>
</div>
<?php endif; ?>
