<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
use common\models\User;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' =>'<i class="fa fa-pencil"></i>',
    'title' => 'Schedule',
	'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Teacher</dt>
	<dd>
		<a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_TEACHER, 'id' => $model->teacherId]) ?>">
		<?= $model->teacher->publicIdentity; ?>
	</a></dd>
	<dt>Date</dt>
	<dd><?= (new \DateTime($model->date))->format('l, F jS, Y'); ?></dd>
	<dt>Time</dt>
	<dd><?= Yii::$app->formatter->asTime($model->date); ?></dd>
	<dt>Duration</dt>
	<dd><?= (new \DateTime($model->duration))->format('H:i'); ?></dd>
	<?php if($model->isUnscheduled()) : ?>
		<dt>Expiry Date</dt>
		<dd><?= Yii::$app->formatter->asDate($model->privateLesson->expiryDate); ?></dd>
	<?php endif; ?>
</dl>
<?php LteBox::end() ?>
