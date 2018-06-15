<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
use yii\widgets\Pjax;

?>
<?php Pjax::begin([
    'id' => 'lesson-discount',
    'timeout' => 6000,
]) ?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => $this->render('_discount-action-menu', [
        'model' => $model,
    ]),
    'title' => 'Discounts',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal lesson-discount">
	<dt class="m-r-10">Customer Discount</dt>
	<dd><?= $model->hasCustomerDiscount() ? $model->customerDiscount->value . ' %' : null; ?></dd>
	<dt class="m-r-10">Line Item Discount</dt>
    <dd><?= $model->hasLineItemDiscount() ? $model->getLineItemDiscountValue() : null; ?></dd>
    <dt class="m-r-10">Payment Frequency</dt>
    <dt> Discount</dt>
	<dd><?= $model->hasEnrolmentPaymentFrequencyDiscount() ? $model->enrolmentPaymentFrequencyDiscount->value . ' %' : null; ?></dd>
	<dt class="m-r-10">Multiple Enrolment</dt>
    <dt> Discount</dt>
	<dd><?= $model->hasMultiEnrolmentDiscount() ? '$ '. $model->multiEnrolmentDiscount->value : null; ?></dd>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>