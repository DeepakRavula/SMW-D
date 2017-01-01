<?php
use yii\helpers\Html;

?>
<div class="col-sm-10">
<div class="panel panel-default">
<div class="panel-heading">
<strong><?= $model->createdUser->publicIdentity; ?></strong> <span class="text-muted"><?= 'created on ' . (new \DateTime($model->createdOn))->format('M. d, Y'); ?></span>
<?= Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>','#', [
	'id' => 'edit-note',
	'class' => 'edit-student-note m-l-20'
]);?>
</div>
<div class="panel-body">
<?= $model->content; ?>
</div><!-- /panel-body -->
</div><!-- /panel panel-default -->
</div><!-- /col-sm-5 -->
