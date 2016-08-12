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
<?php if($remainingOpeningBalance > 0) :?>
	<?= -abs($remainingOpeningBalance);?>
<?php else:?>
 <?= abs($remainingOpeningBalance);?>
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
