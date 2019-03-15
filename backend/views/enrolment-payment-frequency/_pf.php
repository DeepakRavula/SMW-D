<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;

?>
<?php

if ($model->course->isPrivate()) {
	$title = 'Payment Frequency';
} 
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => '<i class="fa fa-pencil edit-enrolment-payment-frequency"></i>',
    'title' => $title,
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<?php if ($model->course->isPrivate()) : ?>
	<dt>Payment Frequency</dt>
	<dd><?= $model->getPaymentFrequency(); ?></dd>
	<?php endif ; ?>
</dl>
<?php LteBox::end()?>