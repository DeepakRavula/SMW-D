<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use yii\helpers\Html;

?>
<?php $boxTools = '<i title="Edit" class="fa fa-pencil add-invoice-note m-r-10"></i>';?> 
<?php if (empty($model->notes)) :?>
<?php $boxTools = '<i title="Add" class="fa fa-plus add-invoice-note m-r-10"></i>';?> <?php endif;?> 
	<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Details',
    'boxTools' => $boxTools,
    'withBorder' => true,
])
?>

<dl class="dl-horizontal">
	<dt>ID</dt>
	<dd><?= $model->getInvoiceNumber(); ?></dd>
	<dt>Date</dt>
	<dd><?= Yii::$app->formatter->asDate($model->date); ?></dd>
	<dt>Status</dt>
	<dd><?= $model->getStatus(); ?></dd>
	<?php if (!$model->isInvoice()) : ?>
	  <dt>Due Date</dt>
		<dd><?= Yii::$app->formatter->asDate($model->dueDate); ?></dd>
	<?php endif; ?>
	<dt>Message</dt>
	<dd><?= $model->notes; ?></dd>
</dl>
<?php LteBox::end()?>
