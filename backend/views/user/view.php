
<?php

use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use common\models\Note;
use yii\helpers\Url;
use common\models\TeacherRoom;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$roleNames = ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'description', 'name');
foreach ($roleNames as $name => $description) {
    if ($name === $searchModel->role_name) {
        $roleName = $description;
    }
}
$this->title = $model->publicIdentity.' - '.ucwords($searchModel->role_name);
$this->params['action-button'] = Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'UserSearch[role_name]' => $searchModel->role_name, 'id' => $model->id, '#' => 'profile'], ['class' => 'btn btn-primary btn-sm']);
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'UserSearch[role_name]' => $searchModel->role_name], ['class' => 'go-back']);
?>
<div class="row">
	<?php
	echo $this->render('_profile', [
		'model' => $model,
		'role' => $roleName,
	]);
	?>
</div>
<div id="discount-warning" style="display:none;" class="alert-warning alert fade in"></div>
<div id="lesson-conflict" style="display:none;" class="alert-danger alert fade in"></div>
<div id="success-notification" style="display:none;" class="alert-success alert fade in"></div>
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
    <div class="nav-tabs-custom">
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
            'model' => $model,
            'accountDataProvider' => $accountDataProvider,
        ]);

        $qualificationContent = $this->render('teacher/_view-qualification', [
			'privateQualificationDataProvider' => $privateQualificationDataProvider,
			'groupQualificationDataProvider' => $groupQualificationDataProvider,
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
		$unavailabilityContent = $this->render('teacher/_unavailability', [
            'unavailabilityDataProvider' => $unavailability,
			'model' => $model,
        ]);
        $openingBalanceContent = $this->render('customer/_opening-balance', [
            'openingBalanceDataProvider' => $openingBalanceDataProvider,
            'openingBalanceCredit' => $openingBalanceCredit,
            'positiveOpeningBalanceModel' => $positiveOpeningBalanceModel,
            'model' => $model,
        ]);

        $unscheduledLessonContent = $this->render('teacher/_unscheduled-lesson', [
            'dataProvider' => $unscheduledLessonDataProvider,
            'model' => $model,
        ]);

        $teacherScheduleContent = $this->render('teacher/_schedule', [
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
		$timeVoucherContent = $this->render('teacher/_time-voucher', [
			'timeVoucherDataProvider' => $timeVoucherDataProvider,
			'searchModel' => $invoiceSearchModel,
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
                'label' => 'Unavailabilities',
                'content' => $unavailabilityContent,
                'options' => [
                    'id' => 'unavailability',
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
                'label' => 'Time Voucher',
                'content' => $timeVoucherContent,
                'options' => [
                    'id' => 'time-voucher',
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

<?php Modal::begin([
    'header' => '<h4 class="m-0">Merge '. $model->publicIdentity . '</h4>',
    'id' => 'customer-merge-modal',
]); ?>
<div id="customer-merge-content"></div>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0"> Edit</h4>',
    'id' => 'user-edit-modal',
]); ?>
<?= $this->render('_form-profile-update', [
	'model' => $model,
]);?>
<?php Modal::end(); ?>
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
    $(document).on('click', '.customer-merge-cancel', function () {
        $('#customer-merge-modal').modal('hide');
        return false;
    });
	$(document).on('click', '.user-edit-button', function () {
        $('#user-edit-modal').modal('show');
        return false;
    });
    $(document).on('click', '#customer-merge', function () {
        $.ajax({
            url    : '<?= Url::to(['customer/merge', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#customer-merge-content').html(response.data);
                    $('#customer-merge-modal').modal('show');
                    $('#warning-notification').html('Merging another customer will \n\
                    delete all of their contact data. This can not be undone.').fadeIn();
                }
            }
        });
        return false;
    });
    $(document).on('beforeSubmit', '#customer-merge-form', function () {
        $.ajax({
            url    : '<?= Url::to(['customer/merge', 'id' => $model->id]); ?>',
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
                    location.reload();
                } else {
                    $('#error-notification').html(response.errors).fadeIn().delay(5000).fadeOut();
                }
            }
        });
        return false;
    });
	$(document).on('click', '.add-group-qualification', function () {
		$('.group-qualification-fields').show();
		$('.hr-quali').hide();
	});
	$(document).on('click', '.add-qualification', function () {
		$('.qualification-fields').show();
		$('.hr-quali').hide();
	});
	
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
				if(response.status) {
                	window.location.href = response.url;
				}
				else {
					$('#student-form').yiiActiveForm('updateMessages',
						response.errors, true);	
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#customer-discount', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
				    $('#success-notification').html(response.message).fadeIn().delay(8000).fadeOut();
				    $('#discount-warning').html(response.data).fadeIn().delay(8000).fadeOut();
        			$.pjax.reload({container:"#customer-log-listing",replace:false,  timeout: 4000});
				}
			}
		});
		return false;
	});
});
</script>