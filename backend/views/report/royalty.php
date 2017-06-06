<?php
/* @var $this yii\web\View */

use common\models\Location;
use yii\helpers\Url;
use common\models\TaxCode;
use common\models\TaxType;

$this->title = 'Royalty';
?>
    <div class="col-xs-12 col-md-6 p-10 p-r-0">
		<?php echo $this->render('_search', ['model' => $searchModel]); ?>
	</div>
	<div class="clearfix"></div>
	<?php
$total = $payments - $invoiceTaxTotal - $royaltyPayment;
$location = Location::findOne(['id' => Yii::$app->session->get('location_id')]);
$advertisement = !empty($location->advertisement->value) ? $location->advertisement->value : 0;
$royalty = !empty($location->royalty->value) ? $location->royalty->value : 0;
$locationDebtAmount = $royalty + $advertisement;
$royaltyAmount = ($total * ($royalty / 100));
$advertisementAmount = ($total * ($advertisement / 100)); 
?>
<style>
.table-invoice-childtable>tbody>tr>td:last-of-type {
    text-align: right;
}
</style>
<div class="col-md-12">
	<div>
		<table cellspacing="0" cellpadding="3" border="0" style="width:40%" class="table-invoice-childtable">
			<tr>
				<td class="p-t-10">Payments Received</td>
				<td class="p-t-10"><?= !empty($payments) ? $payments : 0; ?></td>
			</tr>
			<tr>
				<td class="p-t-10">Tax Collected</td>
				<td class="p-t-10"><?= !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0; ?></td>
			</tr>
			<tr>
				<td class="p-t-10">Royalty Free Items</td>
				<td class="p-t-10"><?= !empty($royaltyPayment) ? $royaltyPayment : 0; ?></td>
			</tr>
			<tr>
				<td class="p-t-10"><strong>Revenue</strong></td>
				<td class="p-t-10"><strong><?= !empty($total) ? $total : 0; ?></strong></td>
			</tr>
			<tr>
				<td class="p-t-10">Advertisement <?= !empty($location->advertisement->value) ? ' (' . $location->advertisement->value . '%)' : ' - '; ?></td>
                <td  class="p-t-10"><?php  if($advertisementAmount>0){ echo round($advertisementAmount, 2);} else { echo '0'; } ?></td>
			</tr>
			<tr>
				<td class="p-t-10">Royalty <?= !empty($location->royalty->value) ? ' (' . $location->royalty->value . '%)' : ' - '; ?></td>
				<td class="p-t-10"><?php  if($royaltyAmount>0){ echo round($royaltyAmount, 2);} else { echo '0'; } ?></td>
			</tr>
			<tr>
				<td class="p-t-10">Subtotal</td>
				<?php $subtotal = $royaltyAmount + $advertisementAmount;?> 
				<td class="p-t-10"><?php  if($subtotal>0){ echo round($subtotal, 2);} else { echo '0'; } ?></td>
			</tr>
			<tr>
				<td class="p-t-10">Tax</td>
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
				<td class="p-t-10"><?php  if($tax>0){ echo round($tax, 2);} else { echo '0'; } ?></td>
			</tr>
			<tr>
				<td class="p-t-10"><strong>Total</strong></td>
				<td><strong><?php  if(($subtotal+$tax)>0){ echo round(($subtotal+$tax), 2);} else { echo '0'; } ?></strong></td>
			</tr>
		</table>
	</div>
	<div class="clearfix"></div>
</div>