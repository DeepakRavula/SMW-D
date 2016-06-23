
<?php

use common\models\User;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

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
$this->title = Yii::t('backend', !($roleName) ? 'User' : $roleName . ' Details');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', !$roleName ? 'User' : $roleName . 's'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row-fluid user-details-wrapper">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left"><?php echo!empty($model->userProfile->firstname) ? $model->userProfile->firstname : null ?>
            <?php echo!empty($model->userProfile->lastname) ? $model->userProfile->lastname : null ?> 
             <em>
                <small><?php echo !empty($model->email) ? $model->email : null ?></small>
            </em> 
        </p>
        <div class="m-l-20 pull-left m-t-5">
            <?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Update Profile'), ['update', 'id' => $model->id,'section' => 'profile'], ['class' => 'm-r-20']) ?>
            <?php
            echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i> Delete'), ['delete', 'id' => $model->id], [
                'class' => '',
                'data' => [
                    'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

<div class="tabbable-panel">
	<div class="tabbable-line">

		<?php $roles = Yii::$app->authManager->getRolesByUser($model->id);
		$role = end($roles); ?>

		<?php
	
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
				'label' => 'Contacts',
				'content' => $addressContent,
				'active' => true,
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
