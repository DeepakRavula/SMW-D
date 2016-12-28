<?php

?>
<div class="col-sm-5">
<div class="panel panel-default">
<div class="panel-heading">
<strong><?= $model->createdUser->publicIdentity; ?></strong> <span class="text-muted"><?= 'created on ' . (new \DateTime($model->createdOn))->format('M. d, Y'); ?></span>
</div>
<div class="panel-body">
<?= $model->content; ?>
</div><!-- /panel-body -->
</div><!-- /panel panel-default -->
</div><!-- /col-sm-5 -->
