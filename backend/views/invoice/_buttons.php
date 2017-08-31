<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\switchinput\SwitchInput;
use backend\models\search\InvoiceSearch;
?>

<?php if ((int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE): ?>
    <?php if ((bool) !$model->isDeleted()): ?>
	<?=	Html::a('<i title="Delete" class="fa fa-trash"></i>', ['delete', 'id' => $model->id],
		[ 
			'class' => 'm-r-10 btn btn-box-tool',
            'data' => [
            	'confirm' => 'Are you sure you want to delete this invoice?',
            	'method' => 'post',
            ],
			'id' => 'delete-button',
		])?>
    <?php endif; ?>
	<?php elseif($model->canRevert()): ?>
        <?=	Html::a('<i class="fa fa-remove"></i> Return', ['revert-invoice', 'id' => $model->id],
		[
			'class' => 'btn btn-primary btn-sm  m-r-10',
            'data' => [
                'confirm' => 'Are you sure you want to return this invoice?',
        	],
			'id' => 'revert-button',
		])
		?>
    <?php endif; ?>
<?= Html::a('<i title="Mail" class="fa fa-envelope-o"></i>', '#', [
	'id' => 'invoice-mail-button',
	'class' => 'm-r-10 btn btn-box-tool']) ?>
<?= Html::a('<i title="Print" class="fa fa-print m-r-10"></i>', ['print/invoice', 'id' => $model->id], ['class' => 'm-r-10 btn btn-box-tool', 'target' => '_blank']) ?>
