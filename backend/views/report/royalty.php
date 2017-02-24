<?php
/* @var $this yii\web\View */

use common\models\Location;

$this->title = 'Royalty';
?>
<?php
$total = $payments - ($invoiceTaxTotal + $royaltyPayment);
$location = Location::findOne(['id' => Yii::$app->session->get('location_id')]);
$advertisement = !empty($location->advertisement->value) ? $location->advertisement->value : 0;
$royalty = !empty($location->royalty->value) ? $location->royalty->value : 0;
$locationDebtAmount = $royalty + $advertisement;
$royaltyAmount = ($total * ($royalty / 100));
$advertisementAmount = ($total * ($advertisement / 100)); 
$netPrice = round(($total - ($royaltyAmount + $advertisementAmount)), 2);
?>
<div class="col-md-12">
    <div class="col-xs-12 col-md-6 p-10 p-r-0">
		<?php echo $this->render('_search', ['model' => $searchModel]); ?>
	</div>
	<div class="clearfix"></div>
	<div>
		<table cellspacing="0" cellpadding="3" border="0" style="width:40%">
			<tr>
				<td class="p-t-10">Payments Received</td>
				<td></td>
				<td class="p-t-10"><?= !empty($payments) ? $payments : 0; ?></td>
			</tr>
			<tr>
				<td class="p-t-10">Tax Collected</td>
				<td></td>
				<td class="p-t-10"><?= !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0; ?></td>
			</tr>
			<tr>
				<td class="p-t-10">Royalty Free Items</td>
				<td></td>
				<td class="p-t-10"><?= !empty($royaltyPayment) ? $royaltyPayment : 0; ?></td>
			</tr>
			<tr>
				<td class="p-t-10">Total</td>
				<td></td>
				<td class="p-t-10"><?= !empty($total) ? $total : 0; ?></td>
			</tr>
			<tr>
			<td class="p-t-10">Advertisement</td>
			<td class="p-t-10"><?= !empty($location->advertisement->value) ? $location->advertisement->value . '%' : ' - '; ?></td>
			<td class="p-t-10"><?= round($advertisementAmount, 2); ?></td>
			</tr>
			<tr>
			<td class="p-t-10">Royalty</td>
			<td class="p-t-10"><?= !empty($location->royalty->value) ? $location->royalty->value . '%' : ' - ';  ?></td>
			<td class="p-t-10"><?= round($royaltyAmount, 2); ?></td>
			</tr>
			<tr>
			<td class="p-t-10"><strong>Owe to head office</strong></td>
			<td></td>
			<td><strong><?= $netPrice; ?></strong></td>
			</tr>
		</table>
	</div>
	<div class="clearfix"></div>
</div>