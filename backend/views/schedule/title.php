<?php
use yii\helpers\Url;

?>
<h1>
	Schedule for <?= (new \DateTime($date))->format('l, F jS, Y') . ' ' . $name; ?>
	<div class="pull-right action-button">
		<?= $this->render('_button'); ?>
	</div>
</h1>
