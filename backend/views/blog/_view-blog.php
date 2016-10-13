<?php
use yii\helpers\Html;

?>
<h2><strong><?= ! empty($model->title) ? $model->title : null ?></strong> </h2>
<div class="author">
	Posted on <?php 
	$postDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
	echo $postDate->format('F j, Y'); ?>
</div>
<div>
	<?= ! empty($model->content) ? $model->content : null ?> 
</div>