<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use yii\helpers\Html;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => $model->getInvoiceNumber(),
	'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Date</dt>
	<dd><?= Yii::$app->formatter->asDate($model->date); ?></dd>
	<dt>Status</dt>
	<dd><?= $model->getStatus(); ?></dd>
	<?php if (!$model->isInvoice()) : ?>
	  <dt>Due Date</dt>
		<dd><?= Yii::$app->formatter->asDate($model->dueDate); ?></dd>
	<?php endif; ?>
</dl>
<?php LteBox::end()?>