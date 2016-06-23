
<?php

use common\models\User;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$roleNames = ArrayHelper::getColumn(
				Yii::$app->authManager->getRoles(), 'description'
);
foreach ($roleNames as $name => $description) {
	if ($name === $searchModel->role_name) {
		$roleName = $description;
	}
}
$this->title = Yii::t('backend', $model->publicIdentity . '(' . (!($roleName) ? 'User' : $roleName) . ')');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', !$roleName ? 'User' : $roleName . 's'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->publicIdentity;
?>

<div class="tabbable-panel">
	<div class="tabbable-line">

		<?php $roles = Yii::$app->authManager->getRolesByUser($model->id);
		$role = end($roles); ?>

		<?php
		$profileContent = $this->render('_view-profile', [
			'model' => $model,
			//'dataProvider1' => $dataProvider1,
		//	'teacherAvailabilityModel' => $teacherAvailabilityModel,
		]);

		$studentContent = null;

		if (!empty($role) && $role->name === User::ROLE_CUSTOMER) {
			$studentContent = $this->render('_student', [
				'model' => $model,
				'dataProvider' => $dataProvider,
				'student' => $student,
			]);
		}

		$addressContent = $this->render('_view-contact', [
			'model' => $model,
			//'dataProvider1' => $dataProvider1,
			'addressDataProvider' => $addressDataProvider,
			'phoneDataProvider' => $phoneDataProvider,
		]);

		$lessonContent = $this->render('_lesson', [
			'model' => $model,
			'lessonDataProvider' => $lessonDataProvider,
		]);

		$enrolmentContent = $this->render('_enrolment', [
			'enrolmentDataProvider' => $enrolmentDataProvider,
		]);

		$invoiceContent = $this->render('_invoice', [
			'invoiceDataProvider' => $invoiceDataProvider,
		]);
        
        $paymentsContent = $this->render('_payments', [
			'paymentsDataProvider' => $paymentsDataProvider,
		]);

		$qualificationContent = $this->render('_view-qualification',[
			'program' => $program,	
		]);

		$teacherAvailabilityContent = $this->render('_view-teacher-availability',[
			'teacherDataProvider' => $teacherDataProvider,
		]);
		?>
		<?php
		$items = [
			[
				'label' => 'Profile',
				'content' => $profileContent,
				'active' => true,
			],
			[
				'label' => 'Contacts',
				'content' => $addressContent,
			],
		];
		if (in_array($role->name, ['teacher'])) {
			$items[] =
			[
				'label' => 'Qualifications',
				'content' => $qualificationContent,
			];
		}
		if (in_array($role->name, ['teacher'])) {
			$items[] =
			[
				'label' => 'Availability',
				'content' => $teacherAvailabilityContent,
			];
		}
		if (in_array($role->name, ['teacher', 'customer'])) {
			$items[] =
			[
				'label' => 'Students',
				'content' => $studentContent,
			];
		}
		if (in_array($role->name, ['customer'])) {
			$items[] =
			[
				'label' => 'Enrolments',
				'content' => $enrolmentContent,
			];
		}
		if (in_array($role->name, ['customer'])) {
			$items[] =
			[
				'label' => 'Lessons',
				'content' => $lessonContent,
			];
		}
		if (in_array($role->name, ['customer'])) {
			$items[] =
			[
				'label' => 'Invoices',
				'content' => $invoiceContent,
			];
		}
        if (in_array($role->name, ['customer'])) {
			$items[] =
			[
				'label' => 'Payments',
				'content' => $paymentsContent,
			];
		}
		?>
		<?php
		//print_r($items);die;
		?>
		<?php
		echo Tabs::widget([
			'items' => $items,
		]);
		?>
		<div class="clearfix"></div>
	</div>
</div>
<script>
	$('.availability').click(function () {
		$('.teacher-availability-create').show();
	});
	$('.add-new-student').click(function () {
		$('.show-create-student-form').show();
	});
	$('.add-address').bind('click', function () {
		$('.address-fields').show();
		$('.hr-ad').hide();
		setTimeout(function () {
			$('.add-address').addClass('add-item');
		}, 100);
	});
	$('.add-phone').bind('click', function () {
		$('.phone-fields').show();
		$('.hr-ph').hide();
		setTimeout(function () {
			$('.add-phone').addClass('add-item-phone');
		}, 100);
	});
</script>
