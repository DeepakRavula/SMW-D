<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use common\models\CustomerDiscount;

?>
<?php 
$boxTools = ['<i class="fa fa-pencil customer-discount-edit-button m-r-10"></i>'];?>
<?php Pjax::begin([
	'id' => 'discount-customer'
]); ?>
	<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'boxTools' => $boxTools,
		'title' => 'Discount',
		'withBorder' => true,
	])
	?>
<?php 
$customerDiscount = CustomerDiscount::findOne(['customerId' => $model->id]);
	$discount = !empty($customerDiscount) ? $customerDiscount->value : null; ?>
	<dl class="dl-horizontal">
		<dt>Discount</dt>
		<dd><?= $discount ?></dd>
	</dl>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>