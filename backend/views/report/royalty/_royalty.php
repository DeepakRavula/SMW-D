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
    <dd><?= !empty($payments) ? Yii::$app->formatter->asDecimal($payments) : Yii::$app->formatter->asDecimal(0); ?></dd>
    <dt>Tax Collected</dt>
    <dd><?= !empty($invoiceTaxTotal) ? Yii::$app->formatter->asDecimal($invoiceTaxTotal) : Yii::$app->formatter->asDecimal(0); ?></dd>
    <dt>Royalty Free Items</dt>
    <dd><?= !empty($royaltyPayment) ? Yii::$app->formatter->asDecimal($royaltyPayment) : Yii::$app->formatter->asDecimal(0); ?></dd>
    <dt>Revenue</dt>
    <dd><?= !empty($total) ? Yii::$app->formatter->asDecimal($total) : Yii::$app->formatter->asDecimal(0); ?></dd>
    <dt>Advertisement <?= !empty($location->advertisement->value) ? ' (' . $location->advertisement->value . '%)' : ' - '; ?></dt>
    <dd><?= Yii::$app->formatter->asDecimal($advertisementAmount); ?></dd>
    <dt>Royalty <?= !empty($location->royalty->value) ? ' (' . $location->royalty->value . '%)' : ' - '; ?></dt>
    <dd><?= Yii::$app->formatter->asDecimal($royaltyAmount); ?></dd>
    <dt>Subtotal</dt>
    <?php $subtotal = $royaltyAmount + $advertisementAmount; ?> 
    <dd><?= Yii::$app->formatter->asDecimal($subtotal); ?></dd>
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
    <dd><?= Yii::$app->formatter->asDecimal($tax); ?></dd>
    <dt>Total</dt>
    <dd><?= Yii::$app->formatter->asDecimal(($subtotal + $tax)); ?></dd>
</dl>
