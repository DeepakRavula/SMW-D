<?php 

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\switchinput\SwitchInput;
?>
<style>
	.bootstrap-switch-id-lesson-present {
		left:530px;
		top:-31px;
	}
	#extra-lesson-delete {
		left:719px;
		top:-78px;	
	}
	.lesson-buttons a{
		margin-bottom:20px;
		width:100%;
		display:block;
	}
	.lesson-buttons a span{
		display:block;
		widrg:100%;
	}
</style>
<div class="row-fluid">
	<div class="col-md-12 action-btns m-b-20 lesson-buttons">
		<?php echo Html::a('<span class="btn btn-info	">Edit</span>', ['update', 'id' => $model->id], ['class' => 'm-r-20 del-ce']) ?>
		<?php if ($model->invoice) : ?>
			<?= Html::a('<span class="btn btn-info">View Invoice</span>', ['invoice/view', 'id' => $model->invoice->id], ['class' => 'm-r-20 del-ce']) ?>
		<?php else : ?>
			<?php echo Html::a('<span class="label label-primary"><i class="fa fa-dollar"></i> Invoice this Lesson</span>', ['invoice', 'id' => $model->id], ['class' => 'm-r-20 del-ce']) ?>
		<?php endif; ?>
		<?php if ($model->isScheduled()) : ?>
			<?php if (!empty($model->proFormaInvoice->id) && $model->proFormaInvoice->isPaid()) : ?>
				<?= Html::a('<span class="label label-primary">View Payment</span>', ['invoice/view', 'id' => $model->proFormaInvoice->id, '#' => 'payment'], ['class' => 'm-r-20 del-ce']) ?>
			<?php else : ?>
				<?php echo Html::a('<span class="label label-primary"><i class="fa fa-dollar"></i> Take Payment</span>', ['lesson/take-payment', 'id' => $model->id], ['class' => 'm-r-20 del-ce']) ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ($model->canExplode()) : ?>
                <?php
                    echo Html::a('<span class="label label-primary"> Explode</span>', ['lesson/split', 'id' => $model->id], [
                            'id' => 'split-lesson',
                            'class' => 'm-r-20 del-ce',
                    ])
                ?>
		<?php endif; ?>
                <?php if ($model->canMerge()) : ?>
                <?php
                    echo Html::a('<span class="label label-primary"> Merge</span>', '#', [
                            'id' => 'merge-lesson',
                            'class' => 'm-r-20 del-ce',
                    ])
                ?>
		<?php endif; ?>
		<?php
		echo Html::a('<i class="fa fa-mail"></i> Email', '#', [
			'id' => 'lesson-mail-button',
			'class' => 'btn bg-purple m-r-20 del-ce'])
		?>	

		<?php
		$lessonDate = (new \DateTime($model->date))->format('Y-m-d');
		$currentDate = (new \DateTime())->format('Y-m-d');
		?>
			<?php $form = ActiveForm::begin(['id' => 'lesson-present-form']); ?>
			<?php $model->present = $model->isMissed() ? false : true; ?> 
		<div>
			<?=
			$form->field($model, 'present')->widget(SwitchInput::classname(), [
				'name' => 'present',
				'pluginOptions' => [
					'handleWidth' => 60,
					'onText' => 'Present',
					'offText' => 'Absent',
				],
			])->label(false);
			?>
		</div>
		<?php ActiveForm::end(); ?>
		<?php if ($model->isDeletable()) : ?>
			<?php
			echo Html::a(' Delete', ['private-lesson/delete', 'id' => $model->id], [
				'id' => 'extra-lesson-delete',
				'class' => (['btn btn-danger m-r-20 del-ce']),
				'data' => [
					'confirm' => 'Are you sure you want to delete this lesson?',
					'method' => 'post',
				],
			]);
			?>	
		<?php endif; ?>
	</div>
</div>