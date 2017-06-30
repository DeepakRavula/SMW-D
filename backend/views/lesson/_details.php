<?php

use common\models\User;
use yii\helpers\Url;
?>
<?php
\insolita\wgadminlte\LteBox::begin([
	'type' => \insolita\wgadminlte\LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil edit-lesson-detail"></i>',
	'title' => 'Details',
])
?>
<strong>Program</strong>
<?= $model->course->program->name; ?><div class='clearfix'></div>
<strong>Teacher</strong>
<a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_TEACHER, 'id' => $model->teacherId]) ?>">
<?= $model->teacher->publicIdentity; ?>
</a>
<?php
\insolita\wgadminlte\LteBox::end()?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Student Merge</h4>',
    'id' => 'edit-lesson-detail-modal',
]); ?>
<div id="student-merge-content"></div>
<?php Modal::end(); ?>
<script>
$(document).ready(function () {
		
});
</script>