<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use yii\helpers\Html;

?>
<?php Pjax::Begin(['id' => 'invoice-details', 'timeout' => 6000]); ?>
<?php $boxTools = '<i title="Edit" class="fa fa-pencil proforma-invoice-detail"></i>';?>
	<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Details',
	'withBorder' => true,
	'boxTools' => $boxTools,
])
?>

<dl class="dl-horizontal">
	<dt>Date</dt>
	<dd><?= Yii::$app->formatter->asDate($model->date); ?></dd>
	<dt>Status</dt>
	<dd><?= $model->getPRStatus();  ?></dd>
	<dt>Due Date</dt>
	<dd><?= Yii::$app->formatter->asDate($model->dueDate); ?></dd>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>