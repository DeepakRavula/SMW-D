<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;

?>
<?php

if ($model->course->isPrivate()) {
	$title = 'Discounts';
} else {
	$title = 'Discounts';
}
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => '<i class="fa fa-pencil edit-enrolment"></i>',
    'title' => $title,
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<?php if ($model->course->isPrivate()) : ?>
	<dt>PF Discount</dt>
	<dd><?= $model->getPaymentFrequencyDiscountValue(); ?></dd>
	<dt>Multiple Enrol. Discount</dt>
	<dd><?= $model->getMultipleEnrolmentDiscountValue(); ?></dd>
	<?php else: ?>
	<dt>Discount</dt>
	<dd><?= $model->getGroupDiscountValue(); ?></dd>
	<?php endif ; ?>
</dl>
<?php LteBox::end()?>