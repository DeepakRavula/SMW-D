<?php
use yii\helpers\Url;
use common\models\User;

?>
<div class="box box-default">
	<div class="box-header with-border">
		<h3 class="box-title">Student</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" ><i class="fa fa-pencil"></i></button>
		</div>
	</div>
<div class="box-body">
	<div class="row">
		<div class="col-md-2">
			<strong>Student</strong>
		</div>
		<div class="col-md-4">
			<a href= "<?= Url::to(['student/view', 'id' => $model->enrolment->student->id]) ?>">
			<?= $model->enrolment->student->fullName; ?>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-2">
			<strong>Customer</strong>
		</div>
		<div class="col-md-4">
			<a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER, 'id' => $model->enrolment->student->customer->id]) ?>">
				<?= $model->enrolment->student->customer->publicIdentity; ?>
			</a>
		</div>
	</div>
	<div class="row">
		<div class="col-md-2">
			<strong>Phone</strong>
		</div>
		<div class="col-md-4">
			<?= !empty($model->enrolment->student->customer->phoneNumber->number) ? $model->enrolment->student->customer->phoneNumber->number : 'None'; ?>
			</a>
		</div>
	</div>
</div>
</div>