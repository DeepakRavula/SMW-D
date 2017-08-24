<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil edit-enrolment"></i>',
	'title' => 'PF & Discounts',
	'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Payment Frequency</dt>
	<dd><?= $model->getPaymentFrequency(); ?></dd>
	<dt>PF Discount</dt>
	<dd><?= $model->getPaymentFrequencyDiscountValue(); ?></dd>
	<dt>Multiple Enrol. Discount</dt>
	<dd><?= $model->getMultipleEnrolmentDiscountValue(); ?></dd>
</dl>
<?php LteBox::end()?>