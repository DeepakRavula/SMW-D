<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;

?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => '<i class="fa fa-pencil edit-enrolment"></i>',
    'title' => 'Discounts',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Customer Discount</dt>
	<dd><?= $model->hasCustomerDiscount() ? $model->customerDiscount->value . ' %' : null; ?></dd>
	<dt>Line Item Discount</dt>
    <dd><?= $model->hasLineItemDiscount() ? $model->getLineItemDiscountValue() : null; ?></dd>
    <dt>PF Discount</dt>
	<dd><?= $model->hasEnrolmentPaymentFrequencyDiscount() ? $model->enrolmentPaymentFrequencyDiscount->value . ' %' : null; ?></dd>
	<dt>Multiple Enrol. Discount</dt>
	<dd><?= $model->hasMultiEnrolmentDiscount() ? '$ '. $model->multiEnrolmentDiscount->value : null; ?></dd>
</dl>
<?php LteBox::end()?>