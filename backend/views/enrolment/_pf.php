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
])
?>
<div class="col-xs-5 p-0"><strong>Payment Frequency</strong></div>
<div class="col-xs-6">
	<?= $model->getPaymentFrequency(); ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-5 p-0"><strong>Payment Frequency Discount</strong></div>
<div class="col-xs-6">
<?= $model->getPaymentFrequencyDiscountValue(); ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-5 p-0"><strong>Multiple Enrolment Discount</strong></div>
<div class="col-xs-6">
	<?= $model->getMultipleEnrolmentDiscountValue(); ?>
</div> 
<?php
LteBox::end()?>