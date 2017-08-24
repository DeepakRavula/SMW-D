<?php
use yii\helpers\Url;
?>
<h1>
	Schedule for <?= (new \DateTime($date))->format('l, F jS, Y'); ?>
	<div class="pull-right action-button">
		<a class="tv-icon" href="<?= Url::to(['schedule/index']); ?>"><i class="fa fa-tv"></i></a>  </div>
	<div class="clearfix"></div>
</h1>
