
<?php

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
        <div class="clearfix"></div>
		<div class="row-fluid"><h6 class="m-0"><em><i class="fa fa-info-circle"></i> Notes:
		<?php echo ! empty($model->userProfile->notes) ? $model->userProfile->notes : null; ?></em>
		</h6>
		</div>	
        <div class="pull-left m-t-10">
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
    <div class="clearfix"></div>
</div>

<div class="tabbable-panel">
	<div class="tabbable-line">

		<?php $roles = Yii::$app->authManager->getRolesByUser($model->id);
		$role = end($roles); ?>

		<?php
	
		$studentContent = $this->render('_customer-student', [
				'model' => $model,
				'dataProvider' => $dataProvider,
				'student' => $student,
		]);

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
        
        $paymentsContent = $this->render('_payments', [
			'paymentsDataProvider' => $paymentsDataProvider,
		]);

		$qualificationContent = $this->render('_view-qualification',[
			'program' => $program,	
			'model' => $model,
		]);

		$teacherAvailabilityContent = $this->render('_view-teacher-availability',[
			'teacherDataProvider' => $teacherDataProvider,
			'model' => $model,
		]);

		$teacherStudentContent = $this->render('_teacher-student',[
			'studentDataProvider' => $studentDataProvider,
		]);

		$staffLocationContent = $this->render('_staff-location',[
			'locationDataProvider' => $locationDataProvider,
		]);
		
		?>
		<?php
		$items = [
			[
				'label' => 'Contact Information',
				'content' => $addressContent,
				'active' => true,
			],
		];

		$teacherItems = [
			[
				'label' => 'Qualifications',
				'content' => $qualificationContent,
			],
			[
				'label' => 'Availability',
				'content' => $teacherAvailabilityContent,
			],
			[
				'label' => 'Students',
				'content' => $teacherStudentContent,
			],	
			
		];
		
		$customerItems = [
			[
				'label' => 'Students',
				'content' => $studentContent,
			],
			[
				'label' => 'Enrolments',
				'content' => $enrolmentContent,
			],
			[
				'label' => 'Lessons',
				'content' => $lessonContent,
			],
			[
				'label' => 'Invoices',
				'content' => $invoiceContent,
			]
		];
		$staffItems = [
			[
				'label' => 'Locations',
				'content' => $staffLocationContent,
			],	
		];
		if (in_array($role->name, ['teacher'])) {
			$items = array_merge($items,$teacherItems);
		}
		
		if (in_array($role->name, ['customer'])) {
			$items = array_merge($items,$customerItems);
		}

		if (in_array($role->name, ['staffmember'])) {
			$items = array_merge($items,$staffItems);
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
