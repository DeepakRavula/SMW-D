<?php
use yii\widgets\Pjax;

$this->registerCssFile("@web/css/note.css");
?>
<?php Pjax::begin(['id' => 'lesson-note-listing']); ?>
<div class="item">
	 <?php if (empty($model->createdUser->userProfile->picture)):?>
	<img src='<?= Yii::getAlias('@backendUrl') . '/img/anonymous.jpg';?>' alt="user image" class="img-circle offline">
	<?php endif; ?>
   <?php if (!empty($model->createdUser->userProfile->getAvatar())):?>
	<img src='<?= $model->createdUser->userProfile->getAvatar()?>'  alt="user image" class="img-circle offline">
	<?php endif; ?>	<p class="message">
		<a class="name">
			<small class="text-muted pull-right"><i class="fa fa-clock-o"></i><?= Yii::$app->formatter->asDatetime($model->createdOn, "php:d M  H:i a"); ?></small>
			<?= $model->createdUser->publicIdentity; ?>
		</a>
		<?= $model->content; ?>
	</p>
</div>
<?php Pjax::end(); ?>


