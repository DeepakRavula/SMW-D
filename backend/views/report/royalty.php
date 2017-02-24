<?php
/* @var $this yii\web\View */

use common\models\Location;

$this->title = 'Royalty';
?>
<?php
	$location = Location::findOne(['id' => Yii::$app->session->get('location_id')]);
	$advertisement = !empty($location->advertisement->value) ? $location->advertisement->value : 0;
	$royalty = !empty($location->royalty->value) ? $location->royalty->value : 0; 
	$locationDebtAmount = $royalty + $advertisement; 
?>
<div class="col-md-12">
    <div class="col-xs-12 col-md-6 p-10 p-r-0">
		<?php echo $this->render('_search', ['model' => $searchModel]); ?>
	</div>
	<div class="clearfix"></div>
	<div> <h4>
	<div> Payments Received : <?= !empty($payments) ? $payments : 0; ?></div>
	<div>Tax Collected : <?= !empty($invoiceTaxTotal) ? $invoiceTaxTotal : 0;?></div>
	<div>Royalty Free Items : <?= !empty($royaltyPayment) ? $royaltyPayment : 0; ?></div>
	<div>Total : <?php $total = $payments - ($invoiceTaxTotal + $royaltyPayment); ?>  <?= !empty($total) ? $total : 0; ?></div>
	<div>Advertisement : <?= !empty($location->advertisement->value) ? $location->advertisement->value . '%' : ' - '; ?></div>
	<div>Royalty : <?= !empty($location->royalty->value) ? $location->royalty->value . '%' : ' - '; ?></div>
	<div>Owe to head office : <?= $total - ($total * ($locationDebtAmount / 100)); ?> </div>
	</h4>
</div>