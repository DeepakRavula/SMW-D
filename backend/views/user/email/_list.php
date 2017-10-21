<?php

use yii\helpers\Html;
?>
<?= Html::hiddenInput('id', $email->id, ['class' => 'email']);?>
<dl class="dl-horizontal">
	<dt><?= $email->label->name; ?></dt>
	<dd><?= $email->email; ?></dd>
</dl>
