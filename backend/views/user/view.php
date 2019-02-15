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
use common\models\CustomerReferralSource;
use insolita\wgadminlte\LteInfoBox;
use insolita\wgadminlte\LteConst;
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
<div id="discount-warning" style="display:none;" class="alert-warning alert fade in"></div>
<div id="lesson-conflict" style="display:none;" class="alert-danger alert fade in"></div>
<div id="success-notification" style="display:none;" class="alert-success alert fade in"></div>
<div id="flash-danger" style="display: none;" class="alert-danger alert fade in"></div>
<div id="flash-success" style="display: none;" class="alert-success alert fade in"></div>
<br>
<?php yii\widgets\Pjax::begin(['id' => 'customer-view']) ?>
<?php if ($searchModel->role_name == 'customer'):?>
<div class="row">
    <div class="col-md-3">  
<?= LteInfoBox::widget([
                      'bgIconColor'=> LteConst::COLOR_RED,
                      'number'=>$invoiceOwingAmountTotal,
                      'text'=>'OWING',
                      'icon'=>'fa fa-exclamation-triangle',
                  ])?>
</div>
<div class="col-md-3">  
<?= LteInfoBox::widget([
                      'bgIconColor'=> LteConst::COLOR_AQUA,
                      'number'=> $credits,
                      'text'=>'CREDITS',
                      'icon'=>'fa fa-check',
                  ])?>
</div>
<div class="col-md-3">  
<?= LteInfoBox::widget([
                      'bgIconColor'=> LteConst::COLOR_GREEN,
                      'number'=> ($lastPayment) ? $lastPayment->amount : null,
                      'text'=>'LAST PAYMENT',
                      'icon'=>'fa fa-credit-card',
                      'description'=> ($lastPayment) ? (new \DateTime($lastPayment->date))->format('M d, Y') : null,
                  ])?>
</div>
<div class="col-md-3">  
<?= LteInfoBox::widget([
                      'bgIconColor'=> LteConst::COLOR_ORANGE,
                      'number'=>$fullyPrePaidLessonsCount,
                      'text'=>'PRE-PAID LESSONS',
                      'icon'=>'fa fa-credit-card',
                  ])?>
</div>
</div>
<?php endif;?>
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
    <?php Pjax::Begin([
        'id' => 'customer-ob'
    ]) ?> 
        <?= $this->render('customer/_opening-balance', [
            'model' => $model,
            'positiveOpeningBalanceModel' => $positiveOpeningBalanceModel,
            'openingBalanceCredit' => $openingBalanceCredit
        ]);
        ?>
    <?php Pjax::end() ?>
        <?=$this->render('customer/_payment-preference', [
            'model' => $model,
        ]); ?>
        <?= $this->render('customer/_invoice', [
            'invoiceDataProvider' => $invoiceDataProvider,
            'count' => $invoiceCount,
            'userModel' => $model,
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
        <?php if ($searchModel->role_name == 'customer'):?>
        <?= $this->render('customer/_payment', [
            'paymentsDataProvider' => $paymentsDataProvider,
            'count' => $paymentCount,
            'userModel' => $model,
        ]);
        ?>
        <?php endif;?>
    </div> 
</div>
    <div class="nav-tabs-custom">
        <?php $roles = Yii::$app->authManager->getRolesByUser($model->id); $role = end($roles); ?>
        <?php
        $noteContent = $this->render('note/view', [
            'model' => new Note(),
            'noteDataProvider' => $noteDataProvider
        ]);
        
        $logContent = $this->render('log', [
            'model' => $model,
            'logDataProvider' => $logDataProvider,
        ]);
        
        $items = [
            [
                'label' => 'History',
                'content' => $logContent,
                'options' => [
                    'id' => 'log',
                ],
            ],
        ];
        
        if (in_array($role->name, ['teacher'])) {
            $timeVoucherContent = $this->render('teacher/_time-voucher', [
                'timeVoucherDataProvider' => $timeVoucherDataProvider,
                'searchModel' => $invoiceSearchModel,
                'model' => $model,
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
                'searchModel' => $searchModel,
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
                    'label' => 'Invoiced Lessons',
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
            $items = array_merge($teacherItems, $items);
        }
        if (in_array($role->name, ['customer'])) {
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

            $proFormaInvoiceContent = $this->render('customer/_pro-forma-invoice', [
                'proFormaInvoiceDataProvider' => $proFormaInvoiceDataProvider,
                'userModel' => $model,
            ]);

//            $paymentContent = $this->render('customer/_account', [
//                'isCustomerView' => $isCustomerView,
//                'model' => $model,
//                'accountDataProvider' => $accountDataProvider,
//                'userModel' => $model,
//                'searchModel' => $searchModel,
//            ]);
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
                    'label' => 'Pro-forma Invoices',
                    'content' => $proFormaInvoiceContent,
                    'options' => [
                        'id' => 'pro-forma-invoice',
                    ],
                ],
//                [
//                    'label' => 'Accounts',
//                    'content' => $paymentContent,
//                    'options' => [
//                        'id' => 'account',
//                    ],
//                ],
                [
                    'label' => 'Comments',
                    'content' => $noteContent,
                    'options' => [
                        'id' => 'comments',
                    ],
                ],
            ];
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

<?php $userForm = new UserForm(); 
    $userForm->setModel($model);
    $userModel = $userForm->getModel();
    if ($userModel->customerReferralSource) {
      $customerReferralSource = $userModel->customerReferralSource;
    } else {
    $customerReferralSource = new CustomerReferralSource();  
    }
    ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0"> Edit</h4>',
    'id' => 'user-edit-modal',
]); ?>
<?= $this->render('update/_profile', [
    'model' => $userForm,
    'userProfile' => $model->userProfile,
    'customerReferralSource' => $customerReferralSource,
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
<?php Modal::end(); $studentId = null?>
<?php \yii\widgets\Pjax::end(); ?>
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

    var qualification = {
        modify :function(url, qualification) {
            var qualificationId = qualification;
            $.ajax({
                url    : url,
                type   : 'get',
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#modal-content').html(response.data);
                        $('#popup-modal').modal('show');
                        if (qualificationId !== undefined) {
                            var param = $.param({ id: qualificationId });
                            var url = '<?= Url::to(['qualification/delete']) ?>?' + param;
                            $('.modal-delete').show();
                            $(".modal-delete").attr("action", url);
                        }
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
                        }
                    }
                }
            });
            return true;
        }
    };

    $(document).on('modal-delete', function(event, params) {
        if ($('#payment-preference-listing').length) {
            $.pjax.reload({container: '#payment-preference-listing', replace:false, async: false, timeout: 6000});
        }
        if ($('#unavailability-list').length) {
            $.pjax.reload({container: '#unavailability-list', replace:false, async: false, timeout: 6000});
        }
        $.pjax.reload({container: '#group-quali-list', replace:false, async: false, timeout: 6000});
        $.pjax.reload({container: '#private-quali-list', replace:false, async: false, timeout: 6000});
    });

    $(document).on('modal-success', function(event, params) {
        if (params.url) {
            window.location.href = params.url;
        }
        if ($('#unavailability-list').length) {
            $.pjax.reload({container: '#unavailability-list', replace:false, async: false, timeout: 6000});
        }
        if ($('#payment-preference-listing').length) {
            $.pjax.reload({container: '#payment-preference-listing', replace:false, async: false, timeout: 6000});
        }
        if ($('#customer-ob').length) {
            $.pjax.reload({container: '#customer-ob', replace:false, async: false, timeout: 6000});
        }
        if ($('#private-quali-list').length) {
            $.pjax.reload({container: '#private-quali-list', replace:false, async: false, timeout: 6000});
        }
        if ($('#group-quali-list').length) {
            $.pjax.reload({container: '#group-quali-list', replace:false, async: false, timeout: 6000});
        }
        return false;
    });

    $(document).on('modal-next', function(event, params) {
        $.pjax.reload({container: "#customer-view", replace: false, async: false, timeout: 6000}); 
        return false;
    });

    $(document).on('click', '.availability', function () {
        $('.teacher-availability-create').show();
    });

    $(document).on('click', '#add-misc-item', function () {
        $('#invoice-line-item-modal').modal('show');
    });

    $(document).on('click', '.add-new-student', function () {
        $.ajax({
            url    : '<?= Url::to(['student/create', 'userId' => $model->id]); ?>',
            type   : 'get',
            success: function(response)
            {
                if (response.status) {
                    $('#popup-modal').modal('show');
                    $('#popup-modal .modal-dialog').css({'width': '400px'});
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Add Student</h4>');
                    $('#modal-content').html(response.data);
                }
            }
        });
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

    $(document).off('click', '.ob-add-btn').on('click', '.ob-add-btn', function () {
        $.ajax({
            url    : '<?= Url::to(['customer/add-opening-balance', 'id' => $model->id]); ?>',
            type   : 'get',
            success: function(response)
            {
                if (response.status) {
                    $('#popup-modal').modal('show');
                    $('#popup-modal .modal-dialog').css({'width': '300px'});
                    $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Opening Balance</h4>');
                    $('#modal-content').html(response.data);
                }
            }
        });
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
                            if (response.status) {
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
                    $('#address-form').yiiActiveForm('updateMessages', response.errors , true);
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
                    $('#modal-content').html(response.data);
                    $('#popup-modal').modal('show');
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
                    $('#email-form').yiiActiveForm('updateMessages', response.errors , true);
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
                    $('#phone-form').yiiActiveForm('updateMessages', response.errors , true);
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

    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (event) {
        if(event.currentTarget.text === 'Availability') {
            $('#availability-calendar').fullCalendar('render');
        }
        if (event.currentTarget.text === 'Schedule') {
            var options = {
                'renderId': '#teacher-schedule-calendar',
                'eventUrl': '<?= Url::to(['teacher-availability/show-lesson-event']) ?>',
                'availabilityUrl': '<?= Url::to(['teacher-availability/availability']) ?>',
                'teacherId': <?= $model->id ?>,
                'size': 'auto'
            };
            $.fn.calendarDayView(options);
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
                if(response.status) {
                    $('.user-note-content').html(response.data);
                    $.pjax.reload({container:"#user-note-listing",replace:false,  timeout: 4000});
                } else {
                    $('#user-note-form').yiiActiveForm('updateMessages', esponse.errors , true);
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
                } else {
                    $('#student-form').yiiActiveForm('updateMessages', response.errors, true); 
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
                if (response.status) {
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
        var isPrimary = parseInt($(this).attr('isPrimary'));
        if (isPrimary) {
            $('#email-error').html('Primary email cannot be deleted').fadeIn().delay(5000).fadeOut();
           return false; 
        }
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
        
    $(document).off('click', '#qualification-grid tbody > tr').on('click', '#qualification-grid tbody > tr', function () {
        var qualificationId = $(this).data('key');
        var url = '<?= Url::to(['qualification/update']); ?>?id=' + qualificationId;
        qualification.modify(url, qualificationId);
    });

    $(document).off('click', '.add-new-group-qualification').on('click', '.add-new-group-qualification', function () {
        var type = 2;
        var teacherId = '<?= $model->id;?>';
        var url = '<?= Url::to(['qualification/create']); ?>?id=' + teacherId + '&type=' + type;
        qualification.modify(url);
    });
    
    $(document).off('click', '.add-new-qualification').on('click', '.add-new-qualification', function () {
        var type = 1;
        var teacherId = '<?= $model->id;?>';
        var url = '<?= Url::to(['qualification/create']); ?>?id=' + teacherId + '&type=' + type;
        qualification.modify(url);
    });

    $(document).on('keyup', '.select2-search__field', function () {
        var searchText = $('.select2-results__option').first().text();
        var tagId = $('.select2-results__option').attr('id');
        if (typeof tagId == 'undefined') {
            $('.select2-results__option').first().attr('id', 'create-new');
            if ($('.select2-results__option').attr('id') == 'create-new') {
                $('#create-new').html('"' + '<b>' + searchText +'</b>'+ '" (create new)');
            }
        }
    });
</script>
