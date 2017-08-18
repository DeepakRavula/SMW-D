<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => 'Attendance',
])
?>
<div class="col-xs-2 p-0"><strong>Present</strong></div>
<div class="col-xs-6">
<?= $model->getPresent(); ?>	
</div> 
<div class='clearfix'></div>
<?php LteBox::end() ?>
					