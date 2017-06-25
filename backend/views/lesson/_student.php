<?php

use yii\helpers\Url;
use common\models\User;
?>
<?php
\insolita\wgadminlte\LteBox::begin([
	'type' => \insolita\wgadminlte\LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil"></i>',
	'title' => 'Student',
])
?>
<strong>Student</strong>
<a href= "<?= Url::to(['student/view', 'id' => $model->enrolment->student->id]) ?>">
	<?= $model->enrolment->student->fullName; ?>
</a>
<div class="clearfix"></div>
<strong>Customer</strong>
<a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER, 'id' => $model->enrolment->student->customer->id]) ?>">
	<?= $model->enrolment->student->customer->publicIdentity; ?>
</a>
<div class="clearfix"></div>
<strong>Phone</strong>
<?= !empty($model->enrolment->student->customer->phoneNumber->number) ? $model->enrolment->student->customer->phoneNumber->number : 'None'; ?>
<?php \insolita\wgadminlte\LteBox::end() ?>
