<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
use common\models\User;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil"></i>',
	'title' => 'Schedule',
])
?>
<div class="col-xs-3 p-0"><strong>Teacher</strong></div>
<div class="col-xs-6">
	<a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_TEACHER, 'id' => $model->teacherId]) ?>">
		<?= $model->teacher->publicIdentity; ?>
	</a>
</div> 
<div class='clearfix'></div>
<div class="col-xs-3 p-0"><strong>Date</strong></div>
<div class="col-xs-6">
<?= (new \DateTime($model->date))->format('l, F jS, Y'); ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-3 p-0"><strong>Time</strong></div>
<div class="col-xs-6">
<?= Yii::$app->formatter->asTime($model->date); ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-3 p-0"><strong>Duration</strong></div>
<div class="col-xs-6">
<?= (new \DateTime($model->duration))->format('H:i'); ?>
</div> 
<div class='clearfix'></div>
<?php if($model->isUnscheduled()) : ?>
<div class="col-xs-3 p-0"><strong>Expiry Date</strong></div>
<div class="col-xs-6">
<?= Yii::$app->formatter->asDate($model->privateLesson->expiryDate); ?>
</div> 
<?php endif; ?>
<?php LteBox::end() ?>
