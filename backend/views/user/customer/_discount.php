<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use common\models\discount\CustomerDiscount;

?>
<?php
$customerDiscount = CustomerDiscount::findOne(['customerId' => $model->id]);
$discount = !empty($customerDiscount) ? $customerDiscount->value : null;

?>
<?php
if (empty($discount)) {
    $boxTools = ['<i class="fa fa-plus customer-discount-button m-r-10"></i>'];
} else {
    $boxTools = ['<i class="fa fa-pencil customer-discount-button m-r-10"></i>'];
}

?>
	<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => $boxTools,
        'title' => 'Discount  (%)',
        'withBorder' => true,
    ])
    ?>

	<dl class="dl-horizontal">
		<dt>Discount</dt>
		<dd><?= $discount ?></dd>
	</dl>
	<?php LteBox::end() ?>