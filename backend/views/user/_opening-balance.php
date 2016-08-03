<?php

use yii\grid\GridView;
use common\models\Payment;
use common\models\Allocation;
use common\models\BalanceLog;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Opening Balance </h4> 
	<div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<hr class="hr-ad right-side-faded hr-payment">

<?php if( ! empty($openingBalancePaymentModel->id)):?>
<div>
Opening Balance: <?= $openingBalancePaymentModel->amount;?>
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
