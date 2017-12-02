<?php

use common\models\Location;
use common\models\TaxCode;
use common\models\TaxType;

$total = $payments - $invoiceTaxTotal - $royaltyPayment;
$location = Location::findOne(['id' => Yii::$app->session->get('location_id')]);
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
	<?= Yii::$app->formatter->format($payments, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>
	</dd>
    <dt>Tax Collected</dt>
    <dd><?php $invoiceTaxTotal = !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0; ?>
	<?= Yii::$app->formatter->format($invoiceTaxTotal, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>
	</dd>
    <dt>Royalty Free Items</dt>
    <dd><?php $royaltyPayment = !empty($royaltyPayment) ? $royaltyPayment : 0; ?>
		<?= Yii::$app->formatter->format($royaltyPayment, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>	
	</dd>
    <dt>Revenue</dt>
    <dd><?php $total = !empty($total) ? $total : 0; ?>
		<?= Yii::$app->formatter->format($total, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>	
	</dd>
    <dt>Advertisement <?= !empty($location->advertisement->value) ? ' (' . $location->advertisement->value . '%)' : ' - '; ?></dt>
    <dd>
	<?= Yii::$app->formatter->format($advertisementAmount, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>		
    <dt>Royalty <?= !empty($location->royalty->value) ? ' (' . $location->royalty->value . '%)' : ' - '; ?></dt>
    <dd>
	<?= Yii::$app->formatter->format($royaltyAmount, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>			
    <dt>Subtotal</dt>
    <?php $subtotal = $royaltyAmount + $advertisementAmount; ?> 
    <dd>
	<?= Yii::$app->formatter->format($subtotal, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>			
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
	<?= Yii::$app->formatter->format($tax, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>		
    <dt>Total</dt>
    <dd>
	<?= Yii::$app->formatter->format($subtotal + $tax, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>			
</dl>
