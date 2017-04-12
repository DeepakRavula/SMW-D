<?php
use yii\helpers\Html;
use kartik\editable\Editable;
use yii\helpers\Url;
?>
<div class="col-sm-10 p-10">
<div class="panel panel-default">
<div class="panel-heading">
<strong><?= $model->createdUser->publicIdentity; ?></strong> <span class="text-muted">
<?php if($model->createdOn !== $model->updatedOn): ?>
<?= 'updated on ' . (new \DateTime($model->updatedOn))->format('M. d, Y @ g:i A'); ?>
<?php else : ?>
<?= 'created on ' . (new \DateTime($model->createdOn))->format('M. d, Y @ g:i A'); ?>
<?php endif; ?></span>
</div>
<div class="panel-body">
<?= Editable::widget( [
    'name' => 'content',
    'asPopover' => true,
    'value' => $model->content,
    'inputType' => Editable::INPUT_TEXTAREA,
    'header' => 'Notes',
	'submitOnEnter' => false,
    'size'=>'lg',
    'options' => ['class'=>'form-control', 'rows'=>5, 'placeholder'=>'Enter notes...'],
    'formOptions' => ['action' => Url::to(['/note/update', 'id' => $model->id])],
]);?>
</div><!-- /panel-body -->
</div><!-- /panel panel-default -->
</div><!-- /col-sm-5 -->
