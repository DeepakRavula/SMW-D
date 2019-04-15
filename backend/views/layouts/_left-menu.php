<?php

use backend\widgets\Menu;
use common\models\User;
use common\models\UserLocation;
use common\models\Program;
use common\models\Lesson;
use common\models\Invoice;
use common\models\Student;
use common\models\Course;
use common\models\log\LogHistory;

?>
<?php
$userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->identity->id]);
$fromDate = (new \DateTime())->format('M d, Y');
$toDate = (new \DateTime())->format('M d, Y');
$dateRange = $fromDate . ' - ' . $toDate;
echo Menu::widget([
    'options' => ['class' => 'sidebar-menu'],
    'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span>{right-icon}{badge}</a>',
    'submenuTemplate' => "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
    'activateParents' => true,
    'items' => [
            [
            'label' => Yii::t('backend', 'Dashboard'),
            'icon' => '<i class="fa fa-tachometer"></i>',
            'url' => ['/dashboard/index'],
            'visible' => Yii::$app->user->can('manageDashboard'),
            'active' => (Yii::$app->controller->id === 'dashboard')
        ],
            [
            'label' => Yii::t('backend', 'Schedule'),
            'icon' => '<i class="fa  fa-calendar"></i>',
            'url' => ['/schedule/index'],
            'visible' => Yii::$app->user->can('manageSchedule'),
            'active' => (Yii::$app->controller->id === 'schedule')
        ],
            [
            'label' => Yii::t('backend', 'Enrolments'),
            'icon' => '<i class="fa  fa-book"></i>',
            'url' => ['/enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false],
            'visible' => Yii::$app->user->can('manageEnrolments'),
            'active' => (Yii::$app->controller->id === 'enrolment')
        ],
            [
            'label' => Yii::t('backend', 'Students'),
            'icon' => '<i class="fa fa-lg fa-fw fa-child"></i>',
            'url' => ['/student/index', 'StudentSearch[showAllStudents]' => false],
            'visible' => Yii::$app->user->can('manageStudents'),
            'active' => (Yii::$app->controller->id === 'student')
        ],
            [
            'label' => Yii::t('backend', 'Customers'),
            'icon' => '<i class="fa fa-lg fa-fw fa-male"></i>',
            'url' => ['/user/index', 'UserSearch[role_name]' => User::ROLE_CUSTOMER],
            'visible' => Yii::$app->user->can('manageCustomers'),
            'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_CUSTOMER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_CUSTOMER)) ? true : false,
        ],
            [
            'label' => Yii::t('backend', 'Teachers'),
            'icon' => '<i class="fa fa-graduation-cap"></i>',
            'url' => ['/user/index', 'UserSearch[role_name]' => User::ROLE_TEACHER],
            'visible' => Yii::$app->user->can('manageTeachers'),
            'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_TEACHER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_TEACHER)) ? true : false,
        ],
        [
            'label' => Yii::t('backend', 'Private Lessons'),
            'url' => ['/lesson/index', 'LessonSearch[dateRange]' => $dateRange],
            'icon' => '<i class="fa fa-music"></i>',
            'visible' => Yii::$app->user->can('managePrivateLessons'),
            'active' => (Yii::$app->controller->id === 'lesson')
        ],
        [
            'label' => Yii::t('backend', 'Group Courses'),
            'url' => ['/course/index', 'CourseSearch[type]' => Lesson::TYPE_GROUP_LESSON],
            'icon' => '<i class="fa fa-music"></i>',
            'visible' => Yii::$app->user->can('manageGroupLessons'),
            'active' => (Yii::$app->controller->id === 'course')
        ],
	[
            'label' => Yii::t('backend', 'Unscheduled Lessons'),
            'url' => ['unscheduled-lesson/index'],
            'icon' => '<i class="fa fa-music"></i>',
	        'active' => (Yii::$app->controller->id === 'unscheduled-lesson')
        ],
        //     [
        //     'label' => Yii::t('backend', 'Proforma Invoices'),
        //     'icon' => '<i class="fa  fa-dollar"></i>',
        //     'url' => ['/invoice/index', 'InvoiceSearch[type]' => Invoice::TYPE_PRO_FORMA_INVOICE],
        //     'visible' => Yii::$app->user->can('managePfi'),
        //     'active' => (isset(Yii::$app->request->queryParams['InvoiceSearch']['type']) && Yii::$app->request->queryParams['InvoiceSearch']['type'] == Invoice::TYPE_PRO_FORMA_INVOICE) ? true : false,
        //     'badge' => Invoice::pfiCount(),
        //     'badgeBgClass' => 'label-default'
        // ],
        
                    [
            'label' => Yii::t('backend', 'Proforma Invoices'),
            'icon' => '<i class="fa  fa-dollar"></i>',
            'url' => ['/invoice/index', 'InvoiceSearch[type]' => Invoice::TYPE_PRO_FORMA_INVOICE],
            'visible' => Yii::$app->user->can('managePfi'),
            'active' => (isset(Yii::$app->request->queryParams['InvoiceSearch']['type']) && Yii::$app->request->queryParams['InvoiceSearch']['type'] == Invoice::TYPE_PRO_FORMA_INVOICE) ? true : false,
        ],
   

	[
	            'label' => Yii::t('backend', 'Payment Preferences'),
            'icon' => '<i class="fa  fa-dollar"></i>',
            'url' => ['/customer-payment-preference/index'],
            'visible' => Yii::$app->user->can('manageAdmin'),
            'active' => (Yii::$app->controller->id === 'customer-payment-preference')
        ],
          
        [
            'label' => Yii::t('backend', 'Payment Requests'),
            'icon' => '<i class="fa  fa-dollar"></i>',
            'url' => ['/proforma-invoice/index'],
            'visible' => Yii::$app->user->can('managePfi'),
            'active' => (Yii::$app->controller->id === 'proforma-invoice')
        ],
            [
            'label' => Yii::t('backend', 'Invoices'),
            'icon' => '<i class="fa  fa-dollar"></i>',
            'url' => ['/invoice/index', 'InvoiceSearch[type]' => Invoice::TYPE_INVOICE],
            'visible' => Yii::$app->user->can('manageInvoices'),
            'active' => (isset(Yii::$app->request->queryParams['InvoiceSearch']['type']) && Yii::$app->request->queryParams['InvoiceSearch']['type'] == Invoice::TYPE_INVOICE) ? true : false,
        ],
        [
            'label' => Yii::t('backend', 'Payments'),
            'icon' => '<i class="fa  fa-dollar"></i>',
            'url' => ['/payment/index', 'PaymentSearch[isDefault]' => true],
            'visible' => Yii::$app->user->can('manageInvoices'),
            'active' => (Yii::$app->controller->id === 'payment')
        ],
            [
            'label' => Yii::t('backend', 'Reports'),
            'url' => '#',
            'icon' => '<i class="fa fa-line-chart"></i>',
            'options' => ['class' => 'treeview'],
            'visible' => Yii::$app->user->can('manageReports'),
            'items' => [
                    [
                    'label' => Yii::t('backend', 'Birthdays'),
                    'icon' => '<i class="fa fa-birthday-cake" aria-hidden="true"></i>',
                    'url' => ['/report/student-birthday'],
					'visible' => Yii::$app->user->can('manageBirthdays'),
                    'active' => (Yii::$app->controller->action->id === 'student-birthday') ? true : false,
                ],
                    [
                    'label' => Yii::t('backend', 'Payments'),
                    'icon' => '<i class="fa fa-dollar"></i>',
                    'url' => ['/report/payment'],
					'visible' => Yii::$app->user->can('managePaymentsReport'),
                    'active' => (Yii::$app->controller->action->id === 'payment') ? true : false,
                ],
                    [
                    'label' => Yii::t('backend', 'Royalty'),
                    'icon' => '<i class="fa fa-dollar"></i>',
                    'url' => ['/report/royalty'],
					'visible' => Yii::$app->user->can('manageRoyalty'),
                    'active' => (Yii::$app->controller->action->id === 'royalty') ? true : false,
                ],
                    [
                    'label' => Yii::t('backend', 'Tax Collected'),
                    'icon' => '<i class="fa fa-cny"></i>',
                    'url' => ['/report/tax-collected'],
					'visible' => Yii::$app->user->can('manageTaxCollected'),
                    'active' => (Yii::$app->controller->action->id === 'tax-collected') ? true : false,
                ],
                                [
                    'label' => Yii::t('backend', 'Royalty Free Items'),
                    'icon' => '<i class="fa fa-cny"></i>',
                    'url' => ['/report/royalty-free'],
					'visible' => Yii::$app->user->can('manageRoyaltyFreeItems'),
                    'active' => (Yii::$app->controller->action->id === 'royalty-free') ? true : false,
                ],
                                [
                    'label' => Yii::t('backend', 'Items by Customer'),
                    'icon' => '<i class="fa fa-cny"></i>',
                    'url' => ['report/customer-items'],
					'visible' => Yii::$app->user->can('manageItemsByCustomer'),
                    'active' => Yii::$app->controller->action->id === 'customer-items'
                ],
                                [
                    'label' => Yii::t('backend', 'Items'),
                    'icon' => '<i class="fa fa-cny"></i>',
                    'url' => ['/report/items'],
					'visible' => Yii::$app->user->can('manageItemReport'),
                    'active' => Yii::$app->controller->action->id === 'items'
                ],
                                [
                    'label' => Yii::t('backend', 'Items Sold by Category'),
                    'icon' => '<i class="fa fa-cny"></i>',
                    'url' => ['/report/item-category'],
					'visible' => Yii::$app->user->can('manageItemCategoryReport'),
                    'active' => Yii::$app->controller->action->id === 'item-category',
                ],
                [
                    'label' => Yii::t('backend', 'Discount'),
                    'icon' => '<i class="fa fa-cny"></i>',
                    'url' => ['/report/discount'],
					'visible' => Yii::$app->user->can('manageDiscountReport'),
                    'active' => Yii::$app->controller->action->id === 'discount',
                ],
                [
                    'label' => Yii::t('backend', 'Sales & Payments Report'),
                    'icon' => '<i class="fa fa-dollar"></i>',
                    'url' => ['/report/sales-and-payment'],
					'visible' => Yii::$app->user->can('manageSalesAndPayment'),
                    'active' => (Yii::$app->controller->action->id === 'sales-and-payment') ? true : false,
                ],
                    [
                    'label' => Yii::t('backend', 'All Locations'),
                    'icon' => '<i class="fa fa-cny"></i>',
                    'url' => ['report/all-locations'],
					'visible' => Yii::$app->user->can('manageAllLocations'),
                ],
            ]
        ],
                [
            'label' => Yii::t('backend', 'Release Notes'),
            'icon' => '<i class="fa fa-sticky-note"></i>',
            'url' => ['release-notes/index'],
            'visible' => Yii::$app->user->can('manageReleaseNotes'),
            'active' => (Yii::$app->controller->id === 'release-notes') ? true : false,
        ],
                [
                        'label' => Yii::t('backend', 'Items'),
                        'icon' => '<i class="fa fa-newspaper-o"></i>',
                        'url' => ['/item/index', 'ItemSearch[showAllItems]' => false],
                    'visible' => Yii::$app->user->can('manageItems'),
                ],
                [
                    'label' => Yii::t('backend', 'Admin'),
                    'url' => '#',
                    'icon' => '<i class="fa fa-user"></i>',
                    'visible' => Yii::$app->user->can('manageAdminArea'),
                    'options' => ['class' => 'treeview'],
                    'items' => [
                            [
                    'label' => Yii::t('backend', 'Administrators'),
                    'icon' => '<i class="fa fa-user-secret"></i>',
					'visible' => Yii::$app->user->can('manageAdmin'),
                    'url' => ['/user/index', 'UserSearch[role_name]' => User::ROLE_ADMINISTRATOR],
                    'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_ADMINISTRATOR || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_ADMINISTRATOR)) ? true : false,
                   
                ],
                [
                    'label' => Yii::t('backend', 'Programs'),
                    'icon' => '<i class="fa fa-table"></i>',
                    'url' => ['/program/index'],
					'visible' => Yii::$app->user->can('managePrograms'),
                    'active' => (Yii::$app->controller->id === 'program') ? true : false,
                   
                ],
                [
                    'label' => Yii::t('backend', 'Cities'),
                    'icon' => '<i class="fa fa-building"></i>',
                    'url' => ['/city/index'],
					'visible' => Yii::$app->user->can('manageCities'),
                ],
                [
                    'label' => Yii::t('backend', 'Provinces'),
                    'icon' => '<i class="fa  fa-upload"></i>',
                    'url' => ['/province/index'],
					'visible' => Yii::$app->user->can('manageProvinces'),
                ],
                [
                    'label' => Yii::t('backend', 'Countries'),
                    'icon' => '<i class="fa fa-globe"></i>',
                    'url' => ['/country/index'],
					'visible' => Yii::$app->user->can('manageCountries'),
                ],
                [
                    'label' => Yii::t('backend', 'Taxes'),
                    'icon' => '<i class="fa  fa-cny"></i>',
                    'url' => ['/tax-code/index'],
					'visible' => Yii::$app->user->can('manageTaxes'),
                ],
                [
                    'label' => Yii::t('backend', 'Color Code'),
                    'icon' => '<i class="fa fa-newspaper-o"></i>',
                    'url' => ['/calendar-event-color/edit'],
					'visible' => Yii::$app->user->can('manageColorCode'),
                ],
                                [
                    'label' => Yii::t('backend', 'Item Category'),
                    'icon' => '<i class="fa fa-newspaper-o"></i>',
                    'url' => ['/item-category/index'],
					'visible' => Yii::$app->user->can('manageItemCategory'),
                ],
                                [
                    'label' => Yii::t('backend', 'Reminder Notes'),
                    'icon' => '<i class="fa  fa-bell"></i>',
                    'url' => ['/reminder-note/index'],
					'visible' => Yii::$app->user->can('manageReminderNotes'),
                ],
                [
                    'label' => Yii::t('backend', 'Blogs'),
                    'icon' => '<i class="fa fa-newspaper-o"></i>',
                    'url' => ['/blog/index'],
					'visible' => Yii::$app->user->can('manageBlogs'),
                ],
                [
                    'label' => Yii::t('backend', 'Locations'),
                    'icon' => '<i class="fa  fa-map-marker"></i>',
                    'url' => ['/location/index'],
					'visible' => Yii::$app->user->can('manageLocations'),
                ],
                [
                    'label' => Yii::t('backend', 'Holidays'),
                    'icon' => '<i class="fa fa-car"></i>',
                    'url' => ['/holiday/index'],
		    'visible' => Yii::$app->user->can('manageHolidays'),
                ],
                [
                    'label' => Yii::t('backend', 'Email Template'),
                    'icon' => '<i class="fa fa-envelope"></i>',
                    'url' => ['/email-template/index'],
                ],
		[
                    'label' => Yii::t('backend', 'Test Email'),
                    'icon' => '<i class="fa fa-envelope"></i>',
                    'url' => ['/test-email/index'],
		    'visible' => env('YII_ENV') === 'dev',
        ],
        [
            'label' => Yii::t('backend', 'Terms of Service'),
            'icon' => '<i class="fa fa-file-text-o"></i>',
            'url' => ['/terms-of-service/index'],
            'visible' => Yii::$app->user->can('manageBlogs'),
            'active' => (Yii::$app->controller->id === 'terms-of-service') ? true : false,
        ],
        [
            'label' => Yii::t('backend', 'Referral Sources'),
            'icon' => '<i class="fa fa-table"></i>',
            'url' => ['/referral-source/index'],
            'visible' => Yii::$app->user->can('manageBlogs'),
            'active' => (Yii::$app->controller->id === 'referral-source') ? true : false,
           
        ],
                
            ]
        ],
        [
            'label' => Yii::t('backend', 'Setup'),
            'url' => '#',
            'icon' => '<i class="fa fa-cogs"></i>',
            'visible' => Yii::$app->user->can('manageSetupArea'),
            'options' => ['class' => 'treeview'],
            'items' => [
                [
                    'label' => Yii::t('backend', 'Privileges'),
                    'icon' => '<i class="fa fa-users"></i>',
                    'url' => ['/permission'],
                    'visible' => Yii::$app->user->can('managePrivileges'),
                    'active' => Yii::$app->controller->id === 'permission',
                ],
                [
                    'label' => Yii::t('backend', 'Staff Members'),
                    'icon' => '<i class="fa fa-users"></i>',
                    'url' => ['/user/index', 'UserSearch[role_name]' => User::ROLE_STAFFMEMBER],
					'visible' => Yii::$app->user->can('manageStaff'),
                    'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_STAFFMEMBER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_STAFFMEMBER)) ? true : false,
                    
                ],
                [
                    'label' => Yii::t('backend', 'Owners'),
                    'icon' => '<i class="fa fa-user"></i>',
                    'url' => ['/user/index', 'UserSearch[role_name]' => User::ROLE_OWNER],
                    'visible' => Yii::$app->user->can('manageOwners'),
                    'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_OWNER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_OWNER)) ? true : false,
                    
                ],

                [
                    'label' => Yii::t('backend', 'Classrooms'),
                    'icon' => '<i class="fa fa-home"></i>',
                    'url' => ['/classroom/index'],
					'visible' => Yii::$app->user->can('manageClassrooms'),
                ],
                [
                    'label' => Yii::t('backend', 'Import'),
                    'icon' => '<i class="fa  fa-upload"></i>',
                    'url' => ['/user/import'],
					'visible' => Yii::$app->user->can('manageImport'),
                ],
                [
                    'label' => Yii::t('backend', 'Location Settings'),
                    'icon' => '<i class="fa  fa-map-marker"></i>',
                    'url' => ['/location-view'],
                    'visible' => Yii::$app->user->can('manageLocations'),
                ]
            ],
        ],
        [
		    
            'label' => Yii::t('backend', 'Timeline'),
            'icon' => '<i class="fa fa-bell"></i>',
            'url' => ['/timeline-event/index'],
        ]
    ]
])
?>
