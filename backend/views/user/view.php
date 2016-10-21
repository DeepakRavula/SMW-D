
<?php

use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$roleNames = ArrayHelper::getColumn(
				Yii::$app->authManager->getRoles(), 'description','name'
);
foreach ($roleNames as $name => $description) {
	if ($name === $searchModel->role_name) {
		$roleName = $description;
	}
} 
$this->title = $model->publicIdentity . ' - ' . ucwords($searchModel->role_name);
$this->params['action-button'] = Html::a('<i class="fa fa-pencil"></i> Edit', ['update','UserSearch[role_name]' => $searchModel->role_name,'id' => $model->id,'#' => 'profile'], ['class' => 'btn btn-primary btn-sm']);
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'UserSearch[role_name]' => $searchModel->role_name], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>
		<div class="row-fluid"><?php if(! empty($model->userProfile->notes)) :?>
			<h5 class="m-0"><em><i class="fa fa-info-circle"></i> Notes:
				<?php echo ! empty($model->userProfile->notes) ? $model->userProfile->notes : null; ?></em>
			</h5>
			<?php endif;?>
		</div>	
        <div class="pull-left m-t-10">
  			 <?php if($searchModel->role_name === 'staffmember'):?>
			 <?php
            echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i> Delete'), ['delete', 'id' => $model->id], [
                'class' => 'abs-delete',
                'data' => [
                    'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
			<?php endif;?>
            <div class="clearfix"></div>
        </div>    
    <div class="clearfix"></div>

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
			'searchModel' => $searchModel
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
			'searchModel' => $searchModel,
			'userModel' => $model,
		]);

		$proFormaInvoiceContent = $this->render('_pro-forma-invoice', [
			'proFormaInvoiceDataProvider' => $proFormaInvoiceDataProvider,
			'userModel' => $model,
		]);
        
        $paymentContent = $this->render('_account', [
			'paymentDataProvider' => $paymentDataProvider,
			'model' => $model,
		]);

		$qualificationContent = $this->render('_view-qualification',[
			'program' => $program,	
			'model' => $model,
			'searchModel' => $searchModel
		]);

		$teacherAvailabilityContent = $this->render('_view-teacher-availability',[
			'teacherDataProvider' => $teacherDataProvider,
			'model' => $model,
			'searchModel' => $searchModel
		]);

		$teacherStudentContent = $this->render('_teacher-student',[
			'studentDataProvider' => $studentDataProvider,
		]);

		$openingBalanceContent = $this->render('_opening-balance',[
			'openingBalanceDataProvider' => $openingBalanceDataProvider,
			'openingBalanceCredit' => $openingBalanceCredit,
			'positiveOpeningBalanceModel' => $positiveOpeningBalanceModel,
			'model' => $model,
		]);
        
        $unscheduledLessonContent = $this->render('_teacher-unscheduled-lesson',[
			'dataProvider' => $unscheduledLessonDataProvider,
		]);

		$teacherScheduleContent = $this->render('_calendar',[
			'teacherId' => $model->id
		]);

		?>
		<?php
		$items = [
			[
				'label' => 'Contact Information',
				'content' => $addressContent,
				'options' => [
                    'id' => 'contact',
                ],
			],
		];

		$teacherItems = [
			[
				'label' => 'Qualifications',
				'content' => $qualificationContent,
				'options' => [
                    'id' => 'qualification',
                ],
			],
			[
				'label' => 'Availability',
				'content' => $teacherAvailabilityContent,
				'options' => [
                    'id' => 'availability',
                ],
			],
            [
                'label' => 'Students',
                'content' => $teacherStudentContent,
                'options' => [
                    'id' => 'student',
                ],
            ],
			[
				'label' => 'Schedule',
				'content' => $teacherScheduleContent,
				'options' => [
                    'id' => 'calendar',
                ],
			],
            [
				'label' => 'Unscheduled Lesson',
				'content' => $unscheduledLessonContent,
				'options' => [
                    'id' => 'unscheduled',
                ],
			],
        ];
		
		$customerItems = [
			[
				'label' => 'Students',
				'content' => $studentContent,
				'options' => [
                    'id' => 'student',
                ],
			],
			[
				'label' => 'Enrolments',
				'content' => $enrolmentContent,
				'options' => [
                    'id' => 'enrolment',
                ],
			],
			[
				'label' => 'Lessons',
				'content' => $lessonContent,
				'options' => [
                    'id' => 'lesson',
                ],
			],
			[
				'label' => 'Invoices',
				'content' => $invoiceContent,
				'options' => [
                    'id' => 'invoice',
                ],
			],
			[
				'label' => 'Pro-forma Invoices',
				'content' => $proFormaInvoiceContent,
				'options' => [
                    'id' => 'pro-forma-invoice',
                ],
			],
			[
				'label' => 'Accounts',
				'content' => $paymentContent,
				'options' => [
                    'id' => 'account',
                ],
			],
			[
				'label' => 'Opening Balance',
				'content' => $openingBalanceContent,
				'options' => [
                    'id' => 'opening-balance',
                ],
			]

		];
		if (in_array($role->name, ['teacher'])) {
			$items = array_merge($items,$teacherItems);
		}
		
		if (in_array($role->name, ['customer'])) {
			$items = array_merge($items,$customerItems);
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
	$('#add-misc-item').click(function(){
		$('#invoice-line-item-modal').modal('show');
  	});   
</script>