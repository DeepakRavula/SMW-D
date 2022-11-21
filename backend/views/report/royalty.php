<?php
/* @var $this yii\web\View */

use common\models\Location;
use yii\helpers\Url;
use common\models\TaxCode;
use common\models\TaxType;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

$this->title = 'Royalty';
?>
<div class="col-xs-12 col-md-6 form-group form-inline">
	<?php echo $this->render('_search', ['model' => $searchModel]); ?>
</div>
<div class="clearfix"></div>
<?php
$total = $payments - $invoiceTaxTotal - $royaltyPayment;
$location = Location::findOne(['id' => \common\models\Location::findOne(['slug' => \Yii::$app->location])->id]);
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
<div class="col-md-12">	
	<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'title' => 'Royalty',
        'withBorder' => true,
    ])
    ?>
	<dl class="dl-horizontal">
		<dt>Payments Received</dt>
		<dd><?= !empty($payments) ? $payments : 0; ?></dd>
		<dt>Tax Collected</dt>
		<dd><?= !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0; ?></dd>
		<dt>Royalty Free Items</dt>
		<dd><?= !empty($royaltyPayment) ? $royaltyPayment : 0; ?></dd>
		<dt>Revenue</dt>
		<dd><?= !empty($total) ? $total : 0; ?></dd>
		<dt>Advertisement <?= !empty($location->advertisement->value) ? ' (' . $location->advertisement->value . '%)' : ' - '; ?></dt>
		<dd><?= round($advertisementAmount, 2); ?></dd>
		<dt>Royalty <?= !empty($location->royalty->value) ? ' (' . $location->royalty->value . '%)' : ' - '; ?></dt>
		<dd><?= round($royaltyAmount, 2); ?></dd>
		<dt>Subtotal</dt>
		<?php $subtotal = $royaltyAmount + $advertisementAmount; ?> 
		<dd><?= round($subtotal, 2); ?></dd>
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
		<dd><?= round($tax, 2); ?></dd>
		<dt>Total</dt>
		<dd><?= round(($subtotal + $tax), 2); ?></dd>
	</dl>
	<?php LteBox::end() ?>
</div> 