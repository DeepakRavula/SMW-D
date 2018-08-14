<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>
<?php Pjax::Begin(['id' => 'lesson-cost'])?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Cost',
    'boxTools' => '<i title="Edit" class="fa fa-pencil edit-cost"></i>',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Cost/hr </dt>
	<dd><?= Yii::$app->formatter->asCurrency($model->teacherRate); ?></dd>
</dl>
<?php LteBox::end() ?>
<?php Pjax::end();?>					