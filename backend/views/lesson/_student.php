<?php

use yii\helpers\Url;
use common\models\User;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => 'Student',
])
?>
<div class="col-xs-2 p-0"><strong>Student</strong></div>
<div class="col-xs-6">
	<a href= "<?= Url::to(['student/view', 'id' => $model->enrolment->student->id]) ?>">
		<?= $model->enrolment->student->fullName; ?>
	</a>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Customer</strong></div>
<div class="col-xs-6">
	<a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER, 'id' => $model->enrolment->student->customer->id]) ?>">
		<?= $model->enrolment->student->customer->publicIdentity; ?>
	</a>
</div> 
<div class='clearfix'></div>
<div class="col-xs-2 p-0"><strong>Phone</strong></div>
<div class="col-xs-6">
	<?= !empty($model->enrolment->student->customer->phoneNumber->number) ? $model->enrolment->student->customer->phoneNumber->number : 'None'; ?>
</div> 
<div class='clearfix'></div>
<?php LteBox::end() ?>
