<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentFrequencyDiscount */

$this->title = 'Edit Discount';

?>
<div class="payment-frequency-discount-update">

    <?php echo $this->render('_form', [
		'paymentFrequencies' => $paymentFrequencies,
    ]) ?>

</div>
