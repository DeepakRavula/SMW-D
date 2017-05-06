<?php
/**
 * @var yii\web\View
 */
use backend\assets\BackendAsset;
use backend\widgets\Menu;
use common\models\TimelineEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\models\Program;
use common\models\Lesson;
use common\models\Location;
use common\models\Invoice;
use common\models\UserLocation;
use yii\web\JsExpression;

$bundle = BackendAsset::register($this);
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
    <div class="wrapper">
      <?= $this->render('_header'); ?>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
               
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <?php echo Menu::widget([
                    'options' => ['class' => 'sidebar-menu'],
                    'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span>{right-icon}{badge}</a>',
                    'submenuTemplate' => "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
                    'activateParents' => true,
                    'items' => [
                       
                        [
                            'label' => Yii::t('backend', 'Dashboard'),
                            'icon' => '<i class="fa fa-tachometer"></i>',
                            'url' => ['dashboard/index'],
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (Yii::$app->controller->id === 'dashboard') ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Schedule'),
                            'icon' => '<i class="fa  fa-calendar"></i>',
                            'url' => ['schedule/index'],
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (Yii::$app->controller->id === 'schedule') ? true : false,
                        ],
						[
                            'label' => Yii::t('backend', 'Enrolments'),
                            'icon' => '<i class="fa  fa-book"></i>',
                            'url' => ['enrolment/index', 'EnrolmentSearch[showAllEnrolments]' => false],
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (Yii::$app->controller->id === 'enrolment') ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Students'),
                            'icon' => '<i class="fa fa-lg fa-fw fa-child"></i>',
                            'url' => ['student/index', 'StudentSearch[showAllStudents]' => false],
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (Yii::$app->controller->id === 'student') ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Customers'),
                            'icon' => '<i class="fa fa-lg fa-fw fa-male"></i>',
                            'url' => ['user/index', 'UserSearch[role_name]' => User::ROLE_CUSTOMER],
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_CUSTOMER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_CUSTOMER)) ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Teachers'),
                            'icon' => '<i class="fa fa-graduation-cap"></i>',
                            'url' => ['user/index', 'UserSearch[role_name]' => User::ROLE_TEACHER],
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_TEACHER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_TEACHER)) ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Staff Members'),
                            'icon' => '<i class="fa fa-users"></i>',
                            'url' => ['user/index', 'UserSearch[role_name]' => User::ROLE_STAFFMEMBER],
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_STAFFMEMBER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_STAFFMEMBER)) ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Owners'),
                            'icon' => '<i class="fa fa-user"></i>',
                            'url' => ['user/index', 'UserSearch[role_name]' => User::ROLE_OWNER],
                            'visible' => Yii::$app->user->can('owner'),
                            'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_OWNER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_OWNER)) ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Administrators'),
                            'icon' => '<i class="fa fa-user-secret"></i>',
                            'url' => ['user/index', 'UserSearch[role_name]' => User::ROLE_ADMINISTRATOR],
                            'visible' => Yii::$app->user->can('administrator'),
                            'active' => (isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name'] == User::ROLE_ADMINISTRATOR || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name'] == User::ROLE_ADMINISTRATOR)) ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Programs'),
                            'icon' => '<i class="fa fa-table"></i>',
                            'url' => ['program/index', 'ProgramSearch[type]' => Program::TYPE_PRIVATE_PROGRAM],
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (Yii::$app->controller->id === 'program') ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Group Courses'),
                            'url' => ['course/index', 'CourseSearch[showAllCourses]' => false],
                            'icon' => '<i class="fa fa-book"></i>',
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (Yii::$app->controller->id === 'group-course') ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Lessons'),
                            'url' => ['lesson/index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON],
                            'icon' => '<i class="fa fa-music"></i>',
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (Yii::$app->controller->id === 'lesson') ? true : false,
                        ],
						[
                            'label' => Yii::t('backend', 'Proforma Invoices'),
                            'icon' => '<i class="fa  fa-dollar"></i>',
                            'url' => ['invoice/index', 'InvoiceSearch[type]' => Invoice::TYPE_PRO_FORMA_INVOICE],
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (Yii::$app->controller->id === 'invoice') ? true : false,

                        ],
                        [
                            'label' => Yii::t('backend', 'Invoices'),
                            'icon' => '<i class="fa  fa-dollar"></i>',
                            'url' => ['invoice/index', 'InvoiceSearch[type]' => Invoice::TYPE_INVOICE],
                            'visible' => Yii::$app->user->can('staffmember'),
                            'active' => (Yii::$app->controller->id === 'invoice') ? true : false,

                        ],
						 [
                            'label' => Yii::t('backend', 'Reports'),
                            'url' => '#',
                            'icon' => '<i class="fa fa-line-chart"></i>',
                            'options' => ['class' => 'treeview'],
                            'visible' => Yii::$app->user->can('owner'),
                            'items' => [
								[
									'label'=>Yii::t('backend', 'Payments'),
									'icon'=>'<i class="fa fa-dollar"></i>',
									'url'=>['report/payment'],    
									'visible'=>Yii::$app->user->can('owner'),
									'active'=>(Yii::$app->controller->action->id === 'payment')? true : false,
                        		],
								[
									'label'=>Yii::t('backend', 'Royalty'),
									'icon'=>'<i class="fa fa-dollar"></i>',
									'url'=>['report/royalty'],    
									'visible'=>Yii::$app->user->can('owner'),
									'active'=>(Yii::$app->controller->action->id === 'royalty')? true : false,
                        		],
								[
									'label'=>Yii::t('backend', 'Tax Collected'),
									'icon'=>'<i class="fa fa-cny"></i>',
									'url'=>['report/tax-collected'],    
									'visible'=>Yii::$app->user->can('owner'),
									'active'=>(Yii::$app->controller->action->id === 'tax-collected')? true : false,
                        		],
								[
									'label'=>Yii::t('backend', 'Royalty Free Items'),
									'icon'=>'<i class="fa fa-cny"></i>',
									'url'=>['report/royalty-free'],    
									'visible'=>Yii::$app->user->can('owner'),
									'active'=>(Yii::$app->controller->action->id === 'royalty-free')? true : false,
                        		],
							]
						],
                        [
                            'label' => Yii::t('backend', 'Release Notes'),
                            'icon' => '<i class="fa fa-sticky-note"></i>',
                            'url' => ['release-notes/index'],
                            'visible' => Yii::$app->user->can('administrator'),
                            'active' => (Yii::$app->controller->id === 'release-notes') ? true : false,
                        ],
                        [
                            'label' => Yii::t('backend', 'Admin'),
                            'options' => ['class' => 'header'],
                        ],
                   
                        [
                            'label' => Yii::t('backend', 'Setup'),
                            'url' => '#',
                            'icon' => '<i class="fa fa-cogs"></i>',
                            'options' => ['class' => 'treeview'],
                            'items' => [
								[
                                    'label' => Yii::t('backend', 'Classrooms'),
                                    'icon' => '<i class="fa fa-home"></i>',
                                    'url' => ['classroom/index'],
                                    'visible' => Yii::$app->user->can('staffmember'),
                                ],
								[
                                    'label' => Yii::t('backend', 'Color Code'),
                                    'icon' => '<i class="fa fa-newspaper-o"></i>',
                                    'url' => ['calendar-event-color/edit'],
                                    'visible' => Yii::$app->user->can('administrator'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Reminder Notes'),
                                    'icon' => '<i class="fa  fa-bell"></i>',
                                    'url' => ['/reminder-note/index'],
                                    'visible' => Yii::$app->user->can('administrator'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Blogs'),
                                    'icon' => '<i class="fa fa-newspaper-o"></i>',
                                    'url' => ['blog/index'],
                                    'visible' => Yii::$app->user->can('administrator'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Locations'),
                                    'icon' => '<i class="fa  fa-map-marker"></i>',
                                    'url' => ['location/index'],
                                    'visible' => Yii::$app->user->can('staffmember'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Holidays'),
                                    'icon' => '<i class="fa fa-car"></i>',
                                    'url' => ['holiday/index'],
                                    'visible' => Yii::$app->user->can('staffmember'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Import'),
                                    'icon' => '<i class="fa  fa-upload"></i>',
                                    'url' => ['user/import'],
                                    'visible' => Yii::$app->user->can('staffmember'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Cities'),
                                    'icon' => '<i class="fa fa-building"></i>',
                                    'url' => ['city/index'],
                                    'visible' => Yii::$app->user->can('staffmember'),

                                ],
                                [
                                    'label' => Yii::t('backend', 'Provinces'),
                                    'icon' => '<i class="fa  fa-upload"></i>',
                                    'url' => ['province/index'],
                                    'visible' => Yii::$app->user->can('staffmember'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Taxes'),
                                    'icon' => '<i class="fa  fa-cny"></i>',
                                    'url' => ['tax-code/index'],
                                    'visible' => Yii::$app->user->can('staffmember'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Countries'),
                                    'icon' => '<i class="fa fa-globe"></i>',
                                    'url' => ['country/index'],
                                    'visible' => Yii::$app->user->can('staffmember'),
                                ],
                                [
                                    'label' => Yii::t('backend', 'Logs'),
                                    'url' => ['log/index'],
                                    'icon' => '<i class="fa fa-angle-double-right"></i>',
                                    'badge' => \backend\models\SystemLog::find()->count(),
                                    'badgeBgClass' => 'label-danger',
                                    'visible' => Yii::$app->user->can('administrator'),
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
                                ['label' => Yii::t('backend', 'Roles'), 'url' => ['admin/role'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                                ['label' => Yii::t('backend', 'Permissions'), 'url' => ['admin/permission'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                                ['label' => Yii::t('backend', 'Assignments'), 'url' => ['admin/assignment'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                                ['label' => Yii::t('backend', 'Routes'), 'url' => ['admin/route'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                                ['label' => Yii::t('backend', 'Rules'), 'url' => ['admin/rule'], 'icon' => '<i class="fa fa-angle-double-right"></i>'],
                            ],
                        ],
                    ],
                ]) ?>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <?php if (isset($this->params['goback'])) : ?>  
                    <div class="pull-left m-r-10">
                        <?php echo $this->params['goback']; ?>
                    </div>
                    <?php endif; ?>
                    <?php echo $this->title ?>
                    
                    <?php if (isset($this->params['action-button'])) : ?>
                        
                        <div class="pull-right m-r-10">
                            <?php echo $this->params['action-button']; ?>
                        </div>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                </h1>

            </section>

            <!-- Main content -->
            <section class="content">
                <?php if (Yii::$app->session->hasFlash('alert')):?>
                    <?php echo \yii\bootstrap\Alert::widget([
                        'body' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                        'options' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                    ])?>
                <?php endif; ?>
                <?php echo $content ?>
            </section><!-- /.content -->           
        </aside><!-- /.right-side -->
    </div><!-- ./wrapper -->

<?php $this->endContent(); ?>
