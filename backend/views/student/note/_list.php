<?php
use yii\helpers\Html;
use dosamigos\editable\Editable;
use yii\helpers\Url;
?>
<div class="col-sm-10">
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
    'value' => $model->content,
    'url' => Url::to(['note/update', 'id' => $model->id]),
    'type' => 'textarea',
    'mode' => 'pop',
    'clientOptions' => [
        'placement' => 'right',
	'showbuttons' => 'bottom',
    ]
]);?>
</div><!-- /panel-body -->
</div><!-- /panel panel-default -->
</div><!-- /col-sm-5 -->
