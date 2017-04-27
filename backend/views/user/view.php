
<?php

use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use common\models\Note;
use yii\helpers\Url;
use common\models\TeacherRoom;
use common\models\TeacherRate;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$roleNames = ArrayHelper::getColumn(
                Yii::$app->authManager->getRoles(), 'description', 'name'
);
foreach ($roleNames as $name => $description) {
    if ($name === $searchModel->role_name) {
        $roleName = $description;
    }
}
$this->title = $model->publicIdentity.' - '.ucwords($searchModel->role_name);
$this->params['action-button'] = Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'UserSearch[role_name]' => $searchModel->role_name, 'id' => $model->id, '#' => 'profile'], ['class' => 'btn btn-primary btn-sm']);
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'UserSearch[role_name]' => $searchModel->role_name], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>
<style>
	.lesson-count {
		font-weight: bold;
	}
    #user-note{
        padding-left:10px;
    }
    .user-note-content .empty{
        padding-left:10px;
    }
    .pagination{
        padding-left:0;
        margin-left:0;
    }

</style>
<div id="flash-danger" style="display: none;" class="alert-danger alert fade in"></div>
<div id="flash-success" style="display: none;" class="alert-success alert fade in"></div>
        <div class="pull-left m-T-10">
  			 <?php if ($searchModel->role_name === 'staffmember'):?>
			 <?php
            echo Html::a(Yii::t('backend', '<i class="fa fa-remove"></i> Delete'), ['delete', 'id' => $model->id], [
                'class' => 'abs-delete',
                'data' => [
                    'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
			<?php endif; ?>
            <div class="clearfix"></div>
        </div>    
    <div class="clearfix"></div>

    <div class="tabbable-panel">
	<div class="tabbable-line">

		<?php $roles = Yii::$app->authManager->getRolesByUser($model->id);
        $role = end($roles); ?>

		<?php

        $studentContent = $this->render('customer/_student', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'student' => $student,
        ]);

        $addressContent = $this->render('_view-contact', [
            'model' => $model,
            'addressDataProvider' => $addressDataProvider,
            'phoneDataProvider' => $phoneDataProvider,
            'searchModel' => $searchModel,
        ]);

        $lessonContent = $this->render('customer/_lesson', [
            'model' => $model,
            'lessonDataProvider' => $lessonDataProvider,
        ]);

        $enrolmentContent = $this->render('customer/_enrolment', [
            'enrolmentDataProvider' => $enrolmentDataProvider,
        ]);

        $invoiceContent = $this->render('customer/_invoice', [
            'invoiceDataProvider' => $invoiceDataProvider,
            'userModel' => $model,
        ]);

        $proFormaInvoiceContent = $this->render('customer/_pro-forma-invoice', [
            'proFormaInvoiceDataProvider' => $proFormaInvoiceDataProvider,
            'userModel' => $model,
        ]);

        $paymentContent = $this->render('customer/_account', [
            'accountDataProvider' => $accountDataProvider,
        ]);

        $qualificationContent = $this->render('teacher/_view-qualification', [
            'program' => $program,
            'groupPrograms' => $groupPrograms,
            'model' => $model,
            'searchModel' => $searchModel,
        ]);

        $teacherAvailabilityContent = $this->render('teacher/_availability-calendar', [
            'model' => $model,
            'minTime' => $minTime,
            'maxTime' => $maxTime,
			'roomModel' => new TeacherRoom(),
        ]);

        $teacherStudentContent = $this->render('teacher/_student', [
            'studentDataProvider' => $studentDataProvider,
        ]);

        $openingBalanceContent = $this->render('customer/_opening-balance', [
            'openingBalanceDataProvider' => $openingBalanceDataProvider,
            'openingBalanceCredit' => $openingBalanceCredit,
            'positiveOpeningBalanceModel' => $positiveOpeningBalanceModel,
            'model' => $model,
        ]);

        $unscheduledLessonContent = $this->render('teacher/_unscheduled-lesson', [
            'dataProvider' => $unscheduledLessonDataProvider,
        ]);

        $teacherScheduleContent = $this->render('teacher/_calendar', [
            'teacherId' => $model->id,
        ]);
		$teacherLessonContent = $this->render('teacher/_view-lesson', [
            'teacherLessonDataProvider' => $teacherLessonDataProvider,
			'searchModel' => $lessonSearchModel,
			'model' => $model,
        ]);
		$noteContent = $this->render('note/view', [
			'model' => new Note(),
			'noteDataProvider' => $noteDataProvider
		]);
		
		$discountContent = $this->render('customer/_discount', [
			'model' => $model,
		]);

		$logContent = $this->render('log', [
			'model' => $model,
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
			[
        	    'label' => 'Logs',
    	        'content' => $logContent,
	            'options' => [
                	'id' => 'log',
            	],
        	],
        ];
		$logItem = $items[1];
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
                'label' => 'Lessons',
                'content' => $teacherLessonContent,
                'options' => [
                    'id' => 'lesson',
                ],
            ],
            [
                'label' => 'Unscheduled Lesson',
                'content' => $unscheduledLessonContent,
                'options' => [
                    'id' => 'unscheduled',
                ],
            ],
			[
        	    'label' => 'Notes',
    	        'content' => $noteContent,
	            'options' => [
                	'id' => 'note',
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
            ],
			[
				'label' => 'Notes',
				'content' => $noteContent,
				'options' => [
					'id' => 'note',
				],
       		],
			[
				'label' => 'Discount',
				'content' => $discountContent,
				'options' => [
					'id' => 'discount',
				],
       		],
        ];
		
        if (in_array($role->name, ['teacher'])) {
            $items = array_merge($items, $teacherItems);
			unset($items[1]);
			array_push($items, $logItem);
        }

        if (in_array($role->name, ['customer'])) {
            $items = array_merge($items, $customerItems);
			unset($items[1]);
			array_push($items, $logItem);
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
		$('#student-create-modal').modal('show');
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
	$(document).ready(function(){
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (event) {
        if(event.currentTarget.text === 'Availability') {
            $('#availability-calendar').fullCalendar('render');
        }
        if (event.currentTarget.text === 'Schedule') {
            $('#calendar').fullCalendar('render');
        }
	});
	$(document).on('click', '#user-note', function (e) {
		$('#note-content').val('');
		$('#user-note-modal').modal('show');
		return false;
  	});
    $(document).on('beforeSubmit', '#user-note-form', function (e) {
		$.ajax({
			url    : '<?= Url::to(['note/create', 'instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_USER]); ?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('.user-note-content').html(response.data);
					$('#user-note-modal').modal('hide');
				}else
				{
				 $('#user-note-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#student-form', function (e) {
		$.ajax({
			url: $(this).attr('action'),
			type: 'post',
			dataType: "json",
			data: $(this).serialize(),
			success: function (response)
			{
				
			}
		});
		return false;
	});
});
</script>