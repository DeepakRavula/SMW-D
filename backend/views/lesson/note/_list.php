<?php
$this->registerCssFile("@web/css/lesson.css");
?>
<?php yii\widgets\Pjax::begin(['id' => 'lesson-note-listing']); ?>
<div class="item">
	<img src='<?= Yii::getAlias('@backendUrl') . '/img/anonymous.jpg';?>' alt="user image" class="img-circle offline">
	<p class="message">
		<a class="name">
			<small class="text-muted pull-right"><i class="fa fa-clock-o"></i><?= Yii::$app->formatter->asTime($model->createdOn); ?></small>
			<?= $model->createdUser->publicIdentity; ?>
		</a>
		<?= $model->content; ?>
	</p>
</div>
<?php yii\widgets\Pjax::end(); ?>


