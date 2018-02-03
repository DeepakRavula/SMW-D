
<?php

use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;
use common\models\Note;
use yii\helpers\Url;
use common\models\TeacherRoom;
use yii\bootstrap\Modal;
use backend\models\UserForm;
use common\models\discount\CustomerDiscount;
use yii\widgets\Pjax;
use common\models\User;
use kartik\date\DatePickerAsset;
use kartik\time\TimePickerAsset;
use kartik\select2\Select2Asset;
Select2Asset::register($this);
TimePickerAsset::register($this);
DatePickerAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\User */

$roleNames = ArrayHelper::getColumn(Yii::$app->authManager->getRoles(), 'description', 'name');
foreach ($roleNames as $name => $description) {
    if ($name === $searchModel->role_name) {
        $roleName = $description;
    }
}
$this->title = $model->publicIdentity;
$this->params['label'] = $this->render('_title', [
    'model' => $model,
    'searchModel' => $searchModel,
    'roleName' => $roleName
]);
$this->params['action-button'] = $this->render('_action-button', [
    'model' => $model,
    'searchModel' => $searchModel,
]);?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<div id="discount-warning" style="display:none;" class="alert-warning alert fade in"></div>
<div id="lesson-conflict" style="display:none;" class="alert-danger alert fade in"></div>
<div id="success-notification" style="display:none;" class="alert-success alert fade in"></div>
<div id="flash-danger" style="display: none;" class="alert-danger alert fade in"></div>
<div id="flash-success" style="display: none;" class="alert-success alert fade in"></div>
<div class="row">
	<div class="col-md-6">	
		<?php
        echo $this->render('_profile', [
            'model' => $model,
            'role' => $roleName,
        ]);
        ?>
        <?php if ($searchModel->role_name === User::ROLE_TEACHER):?>
			<?php Pjax::Begin([
            'id' => 'private-quali-list'
        ]) ?> 
		<?= $this->render('teacher/_private-qualification', [
            'privateQualificationDataProvider' => $privateQualificationDataProvider,
            'groupQualificationDataProvider' => $groupQualificationDataProvider,
            'model' => $model,
            'searchModel' => $searchModel,
        ]);
        ?>
		<?php Pjax::end() ?> 	
			<?php Pjax::Begin([
            'id' => 'group-quali-list'
        ]) ?> 
		<?= $this->render('teacher/_group-qualification', [
            'privateQualificationDataProvider' => $privateQualificationDataProvider,
            'groupQualificationDataProvider' => $groupQualificationDataProvider,
            'model' => $model,
            'searchModel' => $searchModel,
        ]);
        ?>
		<?php Pjax::end() ?> 
		<?php endif;?>
        <?php if ($searchModel->role_name == 'customer'):?>
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
            <?=$this->render('customer/_payment-preference', [
                'model' => $model,
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
	</div> 
</div>
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
                        'logDataProvider'=>$logDataProvider,
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
                'content' => $timeVoucherContent,
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
                'content' => $teacherLessonContent,
                'options' => [
                    'id' => 'time-voucher',
                ],
            ],
            [
                'label' => 'Comments',
                'content' => $noteContent,
                'options' => [
                    'id' => 'comment',
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
    'userProfile' => $model->userProfile,
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
    'header' => '<h4 class="m-0">Email</h4>',
    'id' => 'email-modal',
]); ?>
<div id="email-content"></div>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Phone</h4>',
    'id' => 'phone-modal',
]); ?>
<div id="phone-content"></div>
<?php Modal::end(); ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Address</h4>',
    'id' => 'address-modal',
]); ?>
<div id="address-content"></div>
<?php Modal::end(); ?>
<script>
    var lesson = {
        update :function(params) {
            $.ajax({
                    url: '<?= Url::to(['lesson/update']); ?>?' + params,
                    type: 'get',
                    dataType: "json",
                    success: function (response)
                    {
                        if (response.status)
                        {
                            $('#modal-content').html(response.data);
                            $('#popup-modal').modal('show');
                            $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit schedule</h4>');
                            $('#popup-modal .modal-dialog').css({'width': '1000px'});
                        }
                    }
                });
                return false;
        }
    };
    
	var contactTypes = {
		'email' : 1,
		'phone' : 2,
		'address' : 3,
	};
	var contact = {
        updatePrimary :function(event, val, form, data) {
			var target = event.currentTarget;
			var contactId = $(target).find('li:first').find('.contact').val();
			var id = '<?= $model->id;?>';
			var contactType = $(target).find('li:first').find('.contactType').val();
			var params = $.param({'id':id, 'contactId' : contactId, 'contactType' : contactType});
            $.ajax({
                url: "<?php echo Url::to(['user-contact/update-primary']);?>?" + params,
                type: "POST",
                dataType: "json",
                success: function (response)
                {
					if(response) {
						if(contactType == contactTypes.email) {
	                    	$.pjax.reload({container : '#user-email', timeout : 6000, async : true});
						} else if (contactType == contactTypes.phone) {
    						$.pjax.reload({container : '#user-phone', timeout : 6000, async : true});
						} else {
    						$.pjax.reload({container : '#user-address', timeout : 6000, async : true});
						};
					};
                }
            });
            return true;
        }
    };
	$('.availability').click(function () {
		$('.teacher-availability-create').show();
	});
	$('#add-misc-item').click(function(){
		$('#invoice-line-item-modal').modal('show');
  	});
$(document).ready(function(){
	$.fn.modal.Constructor.prototype.enforceFocus = function() {};
	$(document).on('click', '.add-new-student', function () {
        $('#student-create-modal .modal-dialog').css({'width': '400px'});
		$('#student-create-modal').modal('show');
        return false;
	});
	$(document).on('click', '.address-cancel-btn', function () {
		$('#address-modal').modal('hide');
        return false;
	});
	$(document).on('click', '.phone-cancel-btn', function () {
		$('#phone-modal').modal('hide');
        return false;
	});
    $(document).on('click', '.student-profile-cancel-button', function () {
        $('#student-create-modal').modal('hide');
        return false;
    });
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
    $(document).on('click', '.email-cancel-btn', function () {
        $('#email-modal').modal('hide');
        return false;
    });
	$(document).on('click', '.user-delete-button', function () {
		var id = '<?= $model->id;?>';
		 bootbox.confirm({ 
  			message: "Are you sure you want to delete this user?", 
  			callback: function(result){
				if(result) {
					$('.bootbox').modal('hide');
				$.ajax({
					url: '<?= Url::to(['user/delete']); ?>?id=' + id,
					type: 'post',
					success: function (response)
					{
						if (response.status)
						{
                            window.location.href = response.url;
						} else {
							$('#lesson-conflict').html(response.message).fadeIn().delay(5000).fadeOut();

						}
					}
				});
				return false;	
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
        			$('#address-modal').modal('hide');
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
        			$.pjax.reload({container:"#user-profile",replace:false,  timeout: 6000}).done(function () {
    					$.pjax.reload({container: '#user-header', timeout: 6000});
					});
                } else {
                  $('#user-update-form').yiiActiveForm('updateMessages', response.errors, true); 
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
        			$('#email-modal').modal('hide');
        			$.pjax.reload({container:"#user-email",replace:false,  timeout: 4000});
                    
                } else {
					$('#email-form').yiiActiveForm('updateMessages', response.errors
					, true);
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
        			$('#phone-modal').modal('hide');
        			$.pjax.reload({container:"#user-phone",replace:false,  timeout: 6000});
                } else {
					$('#phone-form').yiiActiveForm('updateMessages', response.errors
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
    $(document).on('click', '.user-contact-delete', function () {
		var contactId = $(this).attr('id') ;
		 bootbox.confirm({ 
  			message: "Are you sure you want to delete?", 
  			callback: function(result){
				if(result) {
					$('.bootbox').modal('hide');
				$.ajax({
					url: '<?= Url::to(['user-contact/delete']); ?>?id=' + contactId,
					type: 'post',
					success: function (response)
					{
						if (response.status)
						{
							if(response.type == contactTypes.email) {
	                    		$('#email-modal').modal('hide');
                            	$.pjax.reload({container: '#user-email', timeout: 6000});
							} else if (response.type == contactTypes.phone) {
								$('#phone-modal').modal('hide');
								$.pjax.reload({container: '#user-phone', timeout: 6000});
							} else {
								$('#address-modal').modal('hide');
								$.pjax.reload({container: '#user-address', timeout: 6000});
							};
						} 
					}
				});
				return false;	
			}
			}
		});	
		return false;
        });
	$(document).on('click', '.add-email, .user-email-edit', function () {
 		var userId = '<?= $model->id;?>';
 		var contactId = $(this).attr('id') ;
 		if (contactId === undefined) {
 			var customUrl = '<?= Url::to(['user-contact/create-email']); ?>?id=' + userId;
 		} else {
 			var customUrl = '<?= Url::to(['user-contact/edit-email']); ?>?id=' + contactId;
 		}
 	  	$.ajax({
 			url    : customUrl,
 			type   : 'post',
 			dataType: "json",
 			data   : $(this).serialize(),
 			success: function(response)
 			{
 				if(response.status)
 				{
 					$('#email-content').html(response.data);
 			        $('#email-modal .modal-dialog').css({'width': '400px'});
 					$('#email-modal').modal('show');
 				}
 			}
 		});
 		return false;
  	});

 	$(document).on('click', '.add-address-btn, .user-address-edit', function () {
 		var userId = '<?= $model->id;?>';
  		var contactId = $(this).attr('id') ;
 
 		if (contactId === undefined) {
 			var customUrl = '<?= Url::to(['user-contact/create-address']); ?>?id=' + userId;
 		} else {
 			var customUrl = '<?= Url::to(['user-contact/edit-address']); ?>?id=' + contactId;
 		}
 	  	$.ajax({
 			url    : customUrl,
 			type   : 'post',
 			dataType: "json",
 			data   : $(this).serialize(),
 			success: function(response)
 			{
 				if(response.status)
 				{
 					$('#address-content').html(response.data);
 			        $('#address-modal .modal-dialog').css({'width': '400px'});
 					$('#address-modal').modal('show');
 				}
 			}
 		});
  		return false;
  	});

 	$(document).on('click', '.add-phone-btn, .user-phone-edit', function () {
 		var userId = '<?= $model->id;?>';
 		var contactId = $(this).attr('id') ;
 		if (contactId === undefined) {
 			var customUrl = '<?= Url::to(['user-contact/create-phone']); ?>?id=' + userId;
 		} else {
 			var customUrl = '<?= Url::to(['user-contact/edit-phone']); ?>?id=' + contactId;
 		}
 	  	$.ajax({
 			url    : customUrl,
 			type   : 'post',
 			dataType: "json",
 			data   : $(this).serialize(),
 			success: function(response)
 			{
 				if(response.status)
 				{
 					$('#phone-content').html(response.data);
 			        $('#phone-modal .modal-dialog').css({'width': '400px'});
 					$('#phone-modal').modal('show');
 				}
 			}
 		});
 		return false;
  	});
	$(document).on("click", '.qualification-cancel', function() {
		$('#qualification-edit-modal').modal('hide');
		$('#group-qualification-modal').modal('hide');
		$('#private-qualification-modal').modal('hide');
		return false;
	});
	$(document).on('click', '.add-new-qualification', function (e) {
		$('#private-qualification-modal').modal('show');
		return false;
	});
	$(document).on('click', '.add-new-group-qualification', function (e) {
		$('#group-qualification-modal').modal('show');
		return false;
	});
	$(document).on("click", "#qualification-grid tbody > tr", function() {
		var qualificationId = $(this).data('key');	
		$.ajax({
			url    : '<?= Url::to(['qualification/update']); ?>?id=' + qualificationId,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('#qualification-edit-content').html(response.data);
					$('#qualification-edit-modal').modal('show');
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#qualification-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
                    $.pjax.reload({container: '#private-grid', timeout: 6000, async:false});
                    $.pjax.reload({container: '#group-grid', timeout: 6000, async:false});
					$('#qualification-edit-modal').modal('hide');
				} else
				{
				 $('#qualification-form').yiiActiveForm('updateMessages', response.errors, true);
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#qualification-form-create', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
                    $.pjax.reload({container: '#private-grid', timeout: 6000});
					$('#private-qualification-modal').modal('hide');
				}else
				{
				 $('#qualification-form-create').yiiActiveForm('updateMessages', response.errors, true);
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#group-qualification-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
                    $.pjax.reload({container: '#group-grid', timeout: 6000});
					$('#group-qualification-modal').modal('hide');
				}else
				{
				 $('#qualification-form-create').yiiActiveForm('updateMessages', response.errors, true);
				}
			}
		});
		return false;
	});
});

$(document).on('change', '#city-label, #address-label, #phone-label, #email-label', function () {
    var activityId = $(this).attr('id');
    var activityName = $(this).attr('name');
    if ($(this).val() == 0) {
        $(this).select2('destroy');
        $(this).prop('disabled', true);
        var labelClass = $(this).parent().find('.control-label').attr('class');
        var labelFor = $(this).parent().find('.control-label').attr('for');
        var labelText = $(this).parent().find('.control-label').text();
        $(this).parent().find('.control-label').remove();
        $("<input type='text'/>").attr("id", activityId).attr("name", activityName).attr("class", 'form-control').prependTo($(this).parent());
        $("<label>").attr('for', labelFor).attr("class", labelClass).text(labelText).prependTo($(this).parent());
        
    }
});
</script>