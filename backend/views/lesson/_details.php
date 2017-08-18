<?php

use common\models\User;
use yii\helpers\Url;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil edit-lesson-detail"></i>',
	'title' => 'Details',
])
?>
<div class="col-xs-2 p-0"><strong>Program</strong></div>
<div class="col-xs-6">
	<?= $model->course->program->name; ?>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Teacher</strong></div>
<div class="col-xs-6">
	<a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_TEACHER, 'id' => $model->teacherId]) ?>">
		<?= $model->teacher->publicIdentity; ?>
	</a>
</div> 
<div class='clearfix'></div>
<?php
LteBox::end()?>