<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\switchinput\SwitchInput;
?>
<?php if ($model->isPrivate()) : ?>
    <?php if ($model->invoice) : ?>
		<?= Html::a('<span class="btn btn-info">View Invoice</span>', ['invoice/view', 'id' => $model->invoice->id], ['class' => 'm-r-10']) ?>
		<?php else : ?>
			<?php echo Html::a('<i class="fa fa-usd"></i>', ['invoice', 'id' => $model->id], ['class' => 'm-r-10 btn btn-box-tool']) ?>
		<?php endif; ?>
		<?php if ($model->isScheduled()) : ?>
			<?php if (!empty($model->proFormaInvoice->id) && $model->proFormaInvoice->isPaid()) : ?>
				<?= Html::a('<span class="btn bg-maroon">View Payment</span>', ['invoice/view', 'id' => $model->proFormaInvoice->id, '#' => 'payment'], ['class' => 'm-r-20 del-ce']) ?>
			<?php else : ?>
				<?php echo Html::a('<i class="fa fa-money"></i>', ['lesson/take-payment', 'id' => $model->id], ['class' => 'm-r-20 btn btn-box-tool']) ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($model->canExplode()) : ?>
                <?php
                    echo Html::a('<span class="btn bg-olive"> Explode</span>', ['lesson/split', 'id' => $model->id], [
                            'id' => 'split-lesson',
                            'class' => 'm-r-20 del-ce',
                    ])
                ?>
		<?php endif; ?>
                <?php if ($model->canMerge()) : ?>
                <?php
                    echo Html::a('<i class="fa fa-chain"></i>', '#', [
                            'id' => 'merge-lesson',
                            'class' => 'm-r-20 btn btn-box-tool',
                    ])
                ?>
		<?php endif; ?>
                <?php endif; ?>
<?= Html::a('<i class="fa fa-envelope"></i>', '#', [
	'id' => 'lesson-mail-button',
	'class' => ' btn btn-box-tool m-r-10'])
?>	
<?php if ($model->isDeletable()) : ?>
	<?= Html::a('<i class="fa fa-trash-o"></i>', ['private-lesson/delete', 'id' => $model->id], [
		'class' => 'btn btn-box-tool m-r-10',
		'id' => 'lesson-delete',
		'data' => [
			'confirm' => 'Are you sure you want to delete this lesson?',
			'method' => 'post',
		],
	])
	?>	
<?php endif; ?>
