<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use yii\helpers\Html;
use backend\models\search\InvoiceSearch;

?>
<?php Pjax::Begin(['id' => 'invoice-details', 'timeout' => 6000]); ?>
<?php $boxTools = '';?>
<?php if ((int) $model->type === InvoiceSearch::TYPE_INVOICE): ?>
<?php $boxTools = '<i title="Edit" class="fa fa-pencil invoice-detail"></i>';?> <?php endif;?>
	<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Details',
	'withBorder' => true,
	'boxTools' => $boxTools,
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
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>