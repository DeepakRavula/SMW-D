<?php

use yii\helpers\Url;
use common\models\User;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Lesson Details',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Program</dt>
	<dd><?= $model->course->program->name; ?></dd>
	<dt>Classroom</dt>
	<dd><?= !empty($model->classroom->name) ? $model->classroom->name : 'None'; ?></dd>
	<dt>Status</dt>
	<dd><?= $model->getStatus(); ?></dd>
    <dt>Online</dt>
    <dd>
     <?= $model->is_online == 1 ? "Yes" : 'No' ; ?>
    </dd>
    <dt>&nbsp;</dt>
    <dd></dd>
</dl>
<?php LteBox::end() ?>