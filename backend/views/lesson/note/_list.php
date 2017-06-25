<style>
 .item>.offline {
    border: 2px solid #dd4b39;
}
.item>img {
    width: 40px;
    height: 40px;
    border: 2px solid transparent;
    border-radius: 50%;
}
 .item>.message {
    margin-left: 55px;
    margin-top: -40px;
}
p {
    margin: 0 0 10px;
}
.item>.message>.name {
    display: block;
    font-weight: 600;
}
</style>
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


