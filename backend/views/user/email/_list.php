<?php

use yii\helpers\Html;
?>
<style>
	.user-email dt {
		font-weight:inherit;
	}
</style>
<?= Html::hiddenInput('id', $email->id, ['class' => 'email']);?>
<div class="<?= !empty($email->isPrimary) ? 'primary' : 'user-email'; ?>">
<dl class="dl-horizontal">
	<dt><?= $email->label->name; ?></dt>
	<dd><?= $email->email; ?></dd>
</dl>
</div>
