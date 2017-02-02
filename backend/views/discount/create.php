<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\PaymentFrequencyDiscount */

$this->title = 'Create Payment Frequency Discount';
$this->params['breadcrumbs'][] = ['label' => 'Payment Frequency Discounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-frequency-discount-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
