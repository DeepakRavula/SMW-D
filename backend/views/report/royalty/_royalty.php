<?php

use common\models\Location;
use common\models\TaxCode;
use common\models\TaxType;

$total = $payments - $invoiceTaxTotal - $royaltyFreeAmount;
$location = Location::findOne(['id' => Location::findOne(['slug' => \Yii::$app->location])->id]);
$advertisement = !empty($location->advertisement->value) ? $location->advertisement->value : 0;
$royalty = !empty($location->royalty->value) ? $location->royalty->value : 0;
$locationDebtAmount = $royalty + $advertisement;
$royaltyAmount = ($total * ($royalty / 100));
$advertisementAmount = ($total * ($advertisement / 100));
if ($total < 0) {
    $royaltyAmount = 0;
    $advertisementAmount = 0;
}

?>

<dl class="dl-horizontal royalty">
    <dt>Payments Received</dt>
    <dd><?php $payments = !empty($payments) ? $payments : 0; ?>
    <?= Yii::$app->formatter->asCurrency(round($payments, 2)); ?>
	</dd>
    <dt>Gift Card Payments</dt>
    <dd><?php $giftCardPayments = !empty($giftCardPayments) ? $giftCardPayments : 0; ?>
    <?= Yii::$app->formatter->asCurrency(round($giftCardPayments, 2)); ?>
	</dd>
    <dt>Tax Collected</dt>
    <dd><?php $invoiceTaxTotal = !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0; ?>
     <?= Yii::$app->formatter->asCurrency(round($invoiceTaxTotal, 2));?>
	</dd>
    <dt>Royalty Free Items</dt>
    <dd><?php $royaltyFreeAmount = !empty($royaltyFreeAmount) ? $royaltyFreeAmount : 0; ?>
    <?= Yii::$app->formatter->asCurrency(round($royaltyFreeAmount, 2)); ?>
	</dd>
    <dt>Revenue</dt>
    <dd><?php $total = !empty($total) ? $total : 0; ?>
    <?= Yii::$app->formatter->asCurrency(round($total, 2)); ?>	
	</dd>
    <dt>Advertisement <?= !empty($location->advertisement->value) ? ' (' . $location->advertisement->value . '%)' : ' - '; ?></dt>
    <dd>
	<?= Yii::$app->formatter->asCurrency(round($advertisementAmount, 2));?>		
    <dt>Royalty <?= !empty($location->royalty->value) ? ' (' . $location->royalty->value . '%)' : ' - '; ?></dt>
    <dd>
	<?= Yii::$app->formatter->asCurrency(round($royaltyAmount, 2));?>			
    <dt>Subtotal</dt>
    <?php $subtotal = round($royaltyAmount, 2) +round($advertisementAmount, 2); ?>  
    <dd>
	<?= Yii::$app->formatter->asCurrency(round($subtotal, 2)); ?>			
    <dt>Tax</dt>
    <?php
    $taxCode = TaxCode::find()
        ->andWhere(['province_id' => $location->province_id,
            'tax_type_id' => TaxType::HST
        ])
        ->orderBy(['id' => SORT_DESC])
        ->one();
    $taxPercentage = $taxCode->rate;
    $tax = $subtotal * ($taxPercentage / 100);

    ?>
    <dd>
	<?= Yii::$app->formatter->asCurrency(round($tax, 2)); ?>		
    <dt>Total</dt>
    <dd>
	<?= Yii::$app->formatter->asCurrency(round(($subtotal + $tax), 2)); ?>			
</dl>
