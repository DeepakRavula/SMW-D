<?php

use backend\widgets\Menu;
use common\models\User;
use common\models\Program;
use common\models\Lesson;
use common\models\Invoice;
use common\models\Student;
use common\models\Course;
use common\models\timelineEvent\TimelineEvent;
?>
<?php
$userLocation = \common\models\UserLocation::findOne(['user_id' => Yii::$app->user->identity->id]);
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
			'visible' => Yii::$app->user->can('viewDashboard'),
			'active' => (Yii::$app->controller->id === 'dashboard') ? true : false,
		],
			[
			'label' => Yii::t('backend', 'Schedule'),
			'icon' => '<i class="fa  fa-calendar"></i>',
			'url' => ['/schedule/index'],
			'visible' => Yii::$app->user->can('viewSchedule'),
			'active' => (Yii::$app->controller->id === 'schedule') ? true : false,
		],
			[
			'label' => Yii::t('backend', 'Enrolments'),
			'icon' => '<i class="fa  fa-book"></i>',
			'url' => ['/enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false],
			'visible' => Yii::$app->user->can('listEnrolment'),
			'active' => (Yii::$app->controller->id === 'enrolment') ? true : false,
		],
			[
			'label' => Yii::t('backend', 'Students'),
			'icon' => '<i class="fa fa-lg fa-fw fa-child"></i>',
			'url' => ['/student/index', 'StudentSearch[showAllStudents]' => false],
			'visible' => Yii::$app->user->can('listStudent'),
			'active' => (Yii::$app->controller->id === 'student') ? true : false,
			'badge' => Student::count(),
			'badgeBgClass' => 'label-default',
		],
			[
			'label' => Yii::t('backend', 'Customers'),
			'icon' => '<i class="fa fa-lg fa-fw fa-male"></i>',
			'url' => ['/user/index', 'UserSearch[role_name]' => User::ROLE_CUSTOMER],
			'visible' => Yii::$app->user->can('listCustomer'),
			'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_CUSTOMER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_CUSTOMER)) ? true : false,
			'badge' => User::customerCount(),
			'badgeBgClass' => 'label-default',
		],
			[
			'label' => Yii::t('backend', 'Teachers'),
			'icon' => '<i class="fa fa-graduation-cap"></i>',
			'url' => ['/user/index', 'UserSearch[role_name]' => User::ROLE_TEACHER],
			'visible' => Yii::$app->user->can('listTeacher'),
			'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_TEACHER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_TEACHER)) ? true : false,
			'badge' => User::teacherCount(),
			'badgeBgClass' => 'label-default'
		],
		[
			'label' => Yii::t('backend', 'Private Lessons'),
			'url' => ['/lesson/index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON],
			'icon' => '<i class="fa fa-music"></i>',
			'visible' => Yii::$app->user->can('listPrivateLesson'),
			'active' => (isset(Yii::$app->request->queryParams['LessonSearch']['type']) && Yii::$app->request->queryParams['LessonSearch']['type'] == Lesson::TYPE_PRIVATE_LESSON) ? true : false,
		],
		[
			'label' => Yii::t('backend', 'Group Lessons'),
			'url' => ['/lesson/index', 'LessonSearch[type]' => Lesson::TYPE_GROUP_LESSON],
			'icon' => '<i class="fa fa-music"></i>',
			'visible' => Yii::$app->user->can('listGroupLesson'),
			'active' => (isset(Yii::$app->request->queryParams['LessonSearch']['type']) && Yii::$app->request->queryParams['LessonSearch']['type'] == Lesson::TYPE_GROUP_LESSON) ? true : false,
		],
			[
			'label' => Yii::t('backend', 'Proforma Invoices'),
			'icon' => '<i class="fa  fa-dollar"></i>',
			'url' => ['/invoice/index', 'InvoiceSearch[type]' => Invoice::TYPE_PRO_FORMA_INVOICE],
			'visible' => Yii::$app->user->can('listPfiInvoice'),
			'active' => (isset(Yii::$app->request->queryParams['InvoiceSearch']['type']) && Yii::$app->request->queryParams['InvoiceSearch']['type'] == Invoice::TYPE_PRO_FORMA_INVOICE) ? true : false,
			'badge' => Invoice::pfiCount(),
			'badgeBgClass' => 'label-default'
		],
			[
			'label' => Yii::t('backend', 'Invoices'),
			'icon' => '<i class="fa  fa-dollar"></i>',
			'url' => ['/invoice/index', 'InvoiceSearch[type]' => Invoice::TYPE_INVOICE],
			'visible' => Yii::$app->user->can('listInvoice'),
			'active' => (isset(Yii::$app->request->queryParams['InvoiceSearch']['type']) && Yii::$app->request->queryParams['InvoiceSearch']['type'] == Invoice::TYPE_INVOICE ) ? true : false,
			'badge' => Invoice::invoiceCount(),
			'badgeBgClass' => 'label-default'
		],
			[
			'label' => Yii::t('backend', 'Reports'),
			'url' => '#',
			'icon' => '<i class="fa fa-line-chart"></i>',
			'options' => ['class' => 'treeview'],
			'visible' => Yii::$app->user->can('viewReport'),
			'items' => [
                    [
                    'label' => Yii::t('backend', 'Birthdays'),
                    'icon' => '<i class="fa fa-birthday-cake" aria-hidden="true"></i>',
                    'url' => ['/report/student-birthday'],
                    'active' => (Yii::$app->controller->action->id === 'student-birthday') ? true : false,
                ],
                    [
					'label' => Yii::t('backend', 'Payments'),
					'icon' => '<i class="fa fa-dollar"></i>',
					'url' => ['/report/payment'],
					'active' => (Yii::$app->controller->action->id === 'payment') ? true : false,
				],
					[
					'label' => Yii::t('backend', 'Royalty'),
					'icon' => '<i class="fa fa-dollar"></i>',
					'url' => ['/report/royalty'],
					'active' => (Yii::$app->controller->action->id === 'royalty') ? true : false,
				],
					[
					'label' => Yii::t('backend', 'Tax Collected'),
					'icon' => '<i class="fa fa-cny"></i>',
					'url' => ['/report/tax-collected'],
					'active' => (Yii::$app->controller->action->id === 'tax-collected') ? true : false,
				],
                                [
					'label' => Yii::t('backend', 'Royalty Free Items'),
					'icon' => '<i class="fa fa-cny"></i>',
					'url' => ['/report/royalty-free'],
					'active' => (Yii::$app->controller->action->id === 'royalty-free') ? true : false,
				],
                                [
					'label' => Yii::t('backend', 'Items by Customer'),
					'icon' => '<i class="fa fa-cny"></i>',
					'url' => ['report/customer-items'],
					'active' => Yii::$app->controller->action->id === 'customer-items'
				],
                                [
					'label' => Yii::t('backend', 'Items'),
					'icon' => '<i class="fa fa-cny"></i>',
					'url' => ['/report/items'],
					'active' => Yii::$app->controller->action->id === 'items'
				],
                                [
					'label' => Yii::t('backend', 'Item Category'),
					'icon' => '<i class="fa fa-cny"></i>',
					'url' => ['/report/item-category'],
					'active' => Yii::$app->controller->action->id === 'item-category',
				],
				[
					'label' => Yii::t('backend', 'Discount'),
					'icon' => '<i class="fa fa-cny"></i>',
					'url' => ['/report/discount'],
					'active' => Yii::$app->controller->action->id === 'discount',
				],
                    [
                    'label' => Yii::t('backend', 'All Locations'),
                    'icon' => '<i class="fa fa-cny"></i>',
                    'url' => ['report/all-locations'],
                ],
            ]
		],
                [
			'label' => Yii::t('backend', 'Release Notes'),
			'icon' => '<i class="fa fa-sticky-note"></i>',
			'url' => ['release-notes/index'],
			'visible' => Yii::$app->user->can('listReleaseNote'),
			'active' => (Yii::$app->controller->id === 'release-notes') ? true : false,
		],
                [
                        'label' => Yii::t('backend', 'Items'),
                        'icon' => '<i class="fa fa-newspaper-o"></i>',
                        'url' => ['/item/index', 'ItemSearch[showAllItems]' => false],
                        'visible' => Yii::$app->user->can('listItem'),
                ],
				[
					'label' => Yii::t('backend', 'Admin'),
					'url' => '#',
					'icon' => '<i class="fa fa-user"></i>',
					'visible' => Yii::$app->user->can('viewAdmin'),
					'options' => ['class' => 'treeview'],
					'items' => [
							[
					'label' => Yii::t('backend', 'Administrators'),
					'icon' => '<i class="fa fa-user-secret"></i>',
					'url' => ['/user/index', 'UserSearch[role_name]' => User::ROLE_ADMINISTRATOR],
					'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_ADMINISTRATOR || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_ADMINISTRATOR)) ? true : false,
					'badge' => User::adminCount(),
					'badgeBgClass' => 'label-default'
				],
				[
					'label' => Yii::t('backend', 'Programs'),
					'icon' => '<i class="fa fa-table"></i>',
					'url' => ['/program/index', 'ProgramSearch[type]' => Program::TYPE_PRIVATE_PROGRAM],
					'active' => (Yii::$app->controller->id === 'program') ? true : false,
					'badge' => Program::find()->active()->count(),
					'badgeBgClass' => 'label-default'
				],
					[
					'label' => Yii::t('backend', 'Privileges'),
					'icon' => '<i class="fa fa-users"></i>',
					'url' => ['/permission'],
					'active' => Yii::$app->controller->id === 'permission',
				],
				[
					'label' => Yii::t('backend', 'Cities'),
					'icon' => '<i class="fa fa-building"></i>',
					'url' => ['/city/index'],
				],
				[
					'label' => Yii::t('backend', 'Provinces'),
					'icon' => '<i class="fa  fa-upload"></i>',
					'url' => ['/province/index'],
				],
				[
					'label' => Yii::t('backend', 'Countries'),
					'icon' => '<i class="fa fa-globe"></i>',
					'url' => ['/country/index'],
				],
				[
					'label' => Yii::t('backend', 'Taxes'),
					'icon' => '<i class="fa  fa-cny"></i>',
					'url' => ['/tax-code/index'],
				],
				[
					'label' => Yii::t('backend', 'Color Code'),
					'icon' => '<i class="fa fa-newspaper-o"></i>',
					'url' => ['/calendar-event-color/edit'],
				],
                                [
					'label' => Yii::t('backend', 'Item Category'),
					'icon' => '<i class="fa fa-newspaper-o"></i>',
					'url' => ['/item-category/index'],
				],
                                [
					'label' => Yii::t('backend', 'Reminder Notes'),
					'icon' => '<i class="fa  fa-bell"></i>',
					'url' => ['/reminder-note/index'],
				],
				[
					'label' => Yii::t('backend', 'Blogs'),
					'icon' => '<i class="fa fa-newspaper-o"></i>',
					'url' => ['/blog/index'],
				],
				[
					'label' => Yii::t('backend', 'Locations'),
					'icon' => '<i class="fa  fa-map-marker"></i>',
					'url' => ['/location/index'],
				],
				[
					'label' => Yii::t('backend', 'Holidays'),
					'icon' => '<i class="fa fa-car"></i>',
					'url' => ['/holiday/index'],
				],
				[
					'label' => Yii::t('backend', 'Text Template'),
					'icon' => '<i class="fa fa-envelope"></i>',
					'url' => ['/text-template/index'],
				],
			],
		],
		[
			'label' => Yii::t('backend', 'Setup'),
			'url' => '#',
			'icon' => '<i class="fa fa-cogs"></i>',
			'visible' => Yii::$app->user->can('viewSetup'),
			'options' => ['class' => 'treeview'],
			'items' => [
					[
					'label' => Yii::t('backend', 'Staff Members'),
					'icon' => '<i class="fa fa-users"></i>',
					'url' => ['/user/index', 'UserSearch[role_name]' => User::ROLE_STAFFMEMBER],
					'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_STAFFMEMBER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_STAFFMEMBER)) ? true : false,
					'badge' => User::staffCount(),
					'badgeBgClass' => 'label-default'
				],
					[
					'label' => Yii::t('backend', 'Owners'),
					'icon' => '<i class="fa fa-user"></i>',
					'url' => ['/user/index', 'UserSearch[role_name]' => User::ROLE_OWNER],
					'visible' => Yii::$app->user->can('listOwner'),
					'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_OWNER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_OWNER)) ? true : false,
					'badge' => User::ownerCount(),
					'badgeBgClass' => 'label-default'
				],
				
					[
					'label' => Yii::t('backend', 'Classrooms'),
					'icon' => '<i class="fa fa-home"></i>',
					'url' => ['/classroom/index'],
				],
					[
					'label' => Yii::t('backend', 'Import'),
					'icon' => '<i class="fa  fa-upload"></i>',
					'url' => ['/user/import'],
				],
				[
					'label' => Yii::t('backend', 'Timeline'),
					'icon' => '<i class="fa fa-bell"></i>',
					'url' => ['/timeline-event/index'],
					'badge' => TimelineEvent::find()
						->andWhere(['locationId' => \common\models\Location::findOne(['slug' => \Yii::$app->location])->id])
						->today()->count(),
					'badgeBgClass' => 'label-default'
				],
			],
		],
			[
			'label' => Yii::t('backend', 'Access Control'),
			'url' => '#',
			'icon' => '<i class="fa fa-edit"></i>',
			'visible' => Yii::$app->user->can('administrator'),
			'options' => ['class' => 'treeview'],
			'items' => [
					['label' => Yii::t('backend', 'Roles'), 'url' => ['/admin/role'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
					['label' => Yii::t('backend', 'Permissions'), 'url' => ['/admin/permission'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
					['label' => Yii::t('backend', 'Assignments'), 'url' => ['/admin/assignment'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
					['label' => Yii::t('backend', 'Routes'), 'url' => ['/admin/route'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
					['label' => Yii::t('backend', 'Rules'), 'url' => ['/admin/rule'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
			],
		],
	],
])
?>
