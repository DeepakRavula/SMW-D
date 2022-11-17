<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>
<?php Pjax::Begin(['id' => 'lesson-attendance'])?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Attendance',
    'boxTools' => '<i title="Edit" class="fa fa-pencil edit-attendance"></i>',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Present</dt>
	<dd><?= $model->getPresent(); ?></dd>
</dl>
<?php LteBox::end() ?>
<?php Pjax::end();?>					