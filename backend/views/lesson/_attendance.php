<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => 'Attendance',
	'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Present</dt>
	<dd><?= $model->getPresent(); ?></dd>
</dl>
<?php LteBox::end() ?>
					