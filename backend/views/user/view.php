
<?php

use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;
use yii\helpers\Html;
use common\models\Note;
use yii\helpers\Url;
use common\models\TeacherRoom;
use yii\bootstrap\Modal;
use backend\models\UserForm;
use common\models\discount\CustomerDiscount;
use yii\widgets\Pjax;
use common\models\User;
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';

/* @var $this yii\web\View */
/* @var $model common\models\User */

$roleNames = ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'description', 'name');
foreach ($roleNames as $name => $description) {
    if ($name === $searchModel->role_name) {
        $roleName = $description;
    }
}
$this->title = $model->publicIdentity.' - '.ucwords($searchModel->role_name);
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'UserSearch[role_name]' => $searchModel->role_name], ['class' => 'go-back']);
?>
<div class="row">
	<div class="col-md-6">	
		<?php
		echo $this->render('_profile', [
			'model' => $model,
			'role' => $roleName,
		]);
		?>
        <?php if($searchModel->role_name == 'customer'):?>
		<?php Pjax::Begin([
			'id' => 'discount-customer'
		]) ?> 
		<?= $this->render('customer/_discount', [
			'model' => $model,
		]);
		?>
		<?php Pjax::end() ?> 
		<?= $this->render('customer/_opening-balance', [
			'model' => $model,
            'positiveOpeningBalanceModel' => $positiveOpeningBalanceModel,
			'openingBalanceCredit' => $openingBalanceCredit
		]);
		?>
    <?php endif;?>
		
	</div> 
	<div class="col-md-6">	
            <?php
		echo $this->render('_email', [
			'model' => $model,
		]);
		?>
		<?php
		echo $this->render('_phone', [
			'model' => $model,
		]);
		?>
        <?= $this->render('_address', [
			'model' => $model,
		]);
		?>
        <?php if ($searchModel->role_name === User::ROLE_CUSTOMER): ?>
            <?=$this->render('customer/_payment-preference', [
                'model' => $model,
            ]);
		?>
<?php endif; ?>
	</div> 

</div>

<div id="discount-warning" style="display:none;" class="alert-warning alert fade in"></div>
<div id="lesson-conflict" style="display:none;" class="alert-danger alert fade in"></div>
<div id="success-notification" style="display:none;" class="alert-success alert fade in"></div>
<div id="flash-danger" style="display: none;" class="alert-danger alert fade in"></div>
<div id="flash-success" style="display: none;" class="alert-success alert fade in"></div>
    <div class="nav-tabs-custom">
		<?php $roles = Yii::$app->authManager->getRolesByUser($model->id);
        $role = end($roles); ?>
		<?php

        $studentContent = $this->render('customer/_student', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'student' => $student,
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
            'userModel'=>$model,
            'searchModel' => $searchModel,
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
        	    'label' => 'History',
    	        'content' => $logContent,
	            'options' => [
                	'id' => 'log',
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
				'label' => 'Comments',
				'content' => $noteContent,
				'options' => [
					'id' => 'comments',
				],
       		],
		  ];
		
        if (in_array($role->name, ['teacher'])) {
            $items = array_merge($teacherItems, $items);
        }

        if (in_array($role->name, ['customer'])) {
            $items = array_merge($customerItems, $items);
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
<?php $userForm = new UserForm();
    $userForm->setModel($model);?>
<?php Modal::begin([
    'header' => '<h4 class="m-0"> Edit</h4>',
    'id' => 'user-edit-modal',
]); ?>
<?= $this->render('update/_profile', [
	'model' => $userForm,
]);?>
<?php Modal::end(); ?>
<?php Pjax::begin([
	'id' => 'customer-discount-pjax'
]); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0"> Discount</h4>',
    'id' => 'customer-discount-edit-modal',
]); ?>
<?= $this->render('customer/_form-discount', [
	'model' => new CustomerDiscount,
    'userModel'=> $model,
]);?>
<?php Modal::end(); ?>
<?php Pjax::end() ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit</h4>',
    'id' => 'edit-phone-modal',
]); ?>
<div id="phone-content"></div>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit</h4>',
    'id' => 'edit-address-modal',
]); ?>
<div id="address-content"></div>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit</h4>',
    'id' => 'edit-email-modal',
]); ?>
<div id="email-content"></div>
<?php Modal::end(); ?>
<script>
	$('.availability').click(function () {
		$('.teacher-availability-create').show();
	});
	$('.add-new-student').click(function () {
		$('#student-create-modal').modal('show');
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
	$(document).on('click', '.ob-cancel', function () {
        $('#ob-modal').modal('hide');
        return false;
    });
	$(document).on('click', '.ob-add-btn', function () {
        $('#ob-modal').modal('show');
        return false;
    });
	$(document).on('click', '.customer-discount-cancel', function () {
        $('#customer-discount-edit-modal').modal('hide');
        return false;
    });
    $(document).on('click', '.customer-discount-button', function () {
        $('#customer-discount-edit-modal').modal('show');
         $('#warning-notification').html('You have entered a \n\
                    non-approved Arcadia discount. All non-approved discounts \n\
                    must be submitted in writing and approved by Head Office \n\
                    prior to entering a discount, otherwise you are in breach \n\
                    of your agreement.').fadeIn();
        return false;
    });
	$(document).on('click', '.phone-cancel-btn', function () {
        $('#edit-phone-modal').modal('hide');
        return false;
    });
	$(document).on('click', '.user-phone-btn', function () {
		$.ajax({
            url    : '<?= Url::to(['user/edit-phone', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#phone-content').html(response.data);
                    $('#edit-phone-modal').modal('show');
                	$('#edit-phone-modal .modal-dialog').css({'width': '800px'});
                }
            }
        });
        return false;
    });
    $(document).on('click', '.user-email-btn', function () {
		$.ajax({
            url    : '<?= Url::to(['user/edit-email', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#email-content').html(response.data);
                    $('#edit-email-modal').modal('show');
                	$('#edit-email-modal .modal-dialog').css({'width': '800px'});
                }
            }
        });
        return false;
    });
	$(document).on('click', '.address-cancel-btn', function () {
        $('#edit-address-modal').modal('hide');
        return false;
    });
    $(document).on('click', '.email-cancel-btn', function () {
        $('#edit-email-modal').modal('hide');
        return false;
    });
	$(document).on('click', '.user-address-btn', function () {
		$.ajax({
            url    : '<?= Url::to(['user/edit-address', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#address-content').html(response.data);
                    $('#edit-address-modal').modal('show');
                	$('#edit-address-modal .modal-dialog').css({'width': '800px'});
                }
            }
        });
        return false;
    });
	$(document).on('beforeSubmit', '#address-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			$('#edit-address-modal').modal('hide');
        			$.pjax.reload({container:"#user-address",replace:false,  timeout: 4000});
                    
                } else {
					$('#address-form').yiiActiveForm('updateMessages', response.errors
					, true);
				}
            }
        });
        return false;
    });
	$(document).on('click', '#user-cancel-btn', function () {
        $('#user-edit-modal').modal('hide');
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
	$(document).on('click', '#customer-discount-delete', function () {
        $.ajax({
            url    : '<?= Url::to(['customer-discount/delete', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#customer-discount-edit-modal').modal('hide');
        			$.pjax.reload({container: '#discount-customer', timeout: 4000}).done(function () {
    					$.pjax.reload({container: '#customer-discount-pjax', timeout: 6000});
					});
                }
            }
        });
        return false;
    });
	$(document).on('beforeSubmit', '#user-update-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			$('#user-edit-modal').modal('hide');
        			$.pjax.reload({container:"#user-profile",replace:false,  timeout: 4000});
                    
                } else {
                  $('#user-update-form').yiiActiveForm('updateMessages', response.errors, true); 
                }
            }
        });
        return false;
    });
	$(document).on('beforeSubmit', '#phone-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			$('#edit-phone-modal').modal('hide');
        			$.pjax.reload({container:"#user-phone",replace:false,  timeout: 4000});
                    
                } else {
					$('#phone-form').yiiActiveForm('updateMessages', response.errors
					, true);
				}
            }
        });
        return false;
    });
    
    $(document).on('beforeSubmit', '#email-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			$('#edit-email-modal').modal('hide');
        			$.pjax.reload({container:"#user-email",replace:false,  timeout: 4000});
                    
                } else {
					$('#email-form').yiiActiveForm('updateMessages', response.errors
					, true);
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
                    $.pjax.reload({container:"#user-note-listing",replace:false,  timeout: 4000});
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
	$(document).on('beforeSubmit', '#customer-discount', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
        			$('#customer-discount-edit-modal').modal('hide');
					$.pjax.reload({container: '#discount-customer', timeout: 4000}).done(function () {
    					$.pjax.reload({container: '#customer-discount-pjax', timeout: 6000});
					});
                } else {
                    $('#error-notification').html(response.errors).fadeIn().delay(5000).fadeOut();
                }
            }
        });
        return false;
    });
});
</script>
