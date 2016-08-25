<?php
/**
 * @var $this yii\web\View
 */
use backend\assets\BackendAsset;
use backend\widgets\Menu;
use common\models\TimelineEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\models\User;
use common\models\Program;
use common\models\Location;
use common\models\Invoice;
use common\models\UserLocation;
use common\models\ReleaseNotes;
use common\models\ReleaseNotesRead;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;

$bundle = BackendAsset::register($this);
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
    <div class="wrapper">
        <!-- header logo: style can be found in header.less -->
        <header class="main-header">
            <a href="<?php echo Yii::getAlias('@frontendUrl') ?>" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->                
                <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/logo.png')) ?>"  />        
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle m-r-10" data-toggle="offcanvas" role="button">
                    <span class="sr-only"><?php echo Yii::t('backend', 'Toggle navigation') ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

				<?php 
				$roles = ArrayHelper::getColumn(
					Yii::$app->authManager->getRolesByUser(Yii::$app->user->id),
						'name'
					);
				$role = end($roles);
				if($role !== User::ROLE_ADMINISTRATOR) {
					$userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id]);
					Yii::$app->session->set('location_id', $userLocation->location_id);
				}
				?>
                    <?php if($role === User::ROLE_ADMINISTRATOR):?>
                        <div class="pull-left">
                            <?php $form = Html::beginForm(); ?>                        
                                 <?= Html::dropDownList('location_id', null,
                                  ArrayHelper::map(Location::find()->all(), 'id', 'name'), ['class' => 'form-control', 'id' => 'location_id', 'options' => [Yii::$app->session->get("location_id") => ['Selected'=>'selected']]
, 'onChange'=> new JsExpression(
                                "$.ajax({
                                    type     :'POST',
                                    cache    : false,
                                    url  : '/location/change-location',
                                    data: {
                                        location_id: $('#location_id').val()
                                    },
                                    success  : function(response) {
                                        location.reload();
                                    }
                                });")]
                            ) ?>
                            <?php Html::endForm() ?>
                            </div>
                        <?php else:?>
                        <?php
                            $userLocationId = Yii::$app->session->get('location_id');
                            $location = Location::findOne(['id' => $userLocationId]);
                            echo '<div class="p-t-15 pull-left" data-toggle="tooltip" data-original-title="Your location" data-placement="bottom"><i class="fa fa-map-marker m-r-10"></i>'.$location->name.'</div>';
                        ?>
                        <?php endif;?>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="notifications-menu" data-toggle="tooltip" data-original-title="Give a feedback" data-placement="bottom">
                            <a href="" onclick="FreshWidget.show(); return false;">
                                <i class="fa fa-comment"></i>
                            </a>
                        </li>
                        <li id="timeline-notifications" class="notifications-menu"  data-toggle="tooltip" data-original-title="Notifications" data-placement="bottom">
                            <a href="<?php echo Url::to(['/timeline-event/index']) ?>">
                                <i class="fa fa-bell"></i>
                                <span class="label label-success">
                                    <?php echo TimelineEvent::find()->today()->count() ?>
                                </span>
                            </a>
                        </li>
                        <!-- Notifications: style can be found in dropdown.less -->
                        <li id="log-dropdown" class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-warning"></i>
                            <span class="label label-danger">
                                <?php echo \backend\models\SystemLog::find()->count() ?>
                            </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header"><?php echo Yii::t('backend', 'You have {num} log items', ['num'=>\backend\models\SystemLog::find()->count()]) ?></li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <?php foreach(\backend\models\SystemLog::find()->orderBy(['log_time'=>SORT_DESC])->limit(5)->all() as $logEntry): ?>
                                            <li>
                                                <a href="<?php echo Yii::$app->urlManager->createUrl(['/log/view', 'id'=>$logEntry->id]) ?>">
                                                    <i class="fa fa-warning <?php echo $logEntry->level == \yii\log\Logger::LEVEL_ERROR ? 'text-red' : 'text-yellow' ?>"></i>
                                                    <?php echo $logEntry->category ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                                <li class="footer">
                                    <?php echo Html::a(Yii::t('backend', 'View all'), ['/log/index']) ?>
                                </li>
                            </ul>
                        </li>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="user-menu"><span><?php echo Yii::$app->user->identity->userProfile->fullName ?> <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header light-blue">
                                    <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>" class="img-circle" alt="User Image" />
                                    <p>
                                        <?php echo Yii::$app->user->identity->userProfile->fullName ?>
                                        <small>
                                            <?php echo Yii::t('backend', 'Member since {0, date, short}', Yii::$app->user->identity->created_at) ?>
                                        </small>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <?php echo Html::a(Yii::t('backend', 'Profile'), ['/sign-in/profile'], ['class'=>'btn btn-default btn-flat']) ?>
                                    </div>
                                    <div class="pull-right">
                                        <?php echo Html::a(Yii::t('backend', 'Logout'), ['/sign-in/logout'], ['class'=>'btn btn-default btn-flat', 'data-method' => 'post']) ?>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <?php echo Html::a('<i class="fa fa-cogs"></i>', ['/site/settings'])?>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->
                <div class="user-panel">
                    <div class="pull-left image">
                        <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>" class="img-circle" />
                    </div>
                    <div class="pull-left info">
                        <p><?php echo Yii::t('backend', '{username}', ['username'=>Yii::$app->user->identity->userProfile->fullName]) ?></p>
                        <a href="<?php echo Url::to(['/sign-in/profile']) ?>">
                            <i class="fa fa-circle text-success"></i>
                            <?php echo Yii::$app->formatter->asDatetime(time()) ?>
                        </a>
                    </div>
                </div>
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <?php echo Menu::widget([
                    'options'=>['class'=>'sidebar-menu'],
                    'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span>{right-icon}{badge}</a>',
                    'submenuTemplate'=>"\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
                    'activateParents'=>true,
                    'items'=>[
                        [
                            'label'=>Yii::t('backend', 'Main'),
                            'options' => ['class' => 'header']
                        ],
						[
                            'label'=>Yii::t('backend', 'Schedule'),
                            'icon'=>'<i class="fa  fa-calendar"></i>',
                            'url'=>['/schedule/index'],
                            'visible'=>Yii::$app->user->can('staffmember'),
                            'active'=>(Yii::$app->controller->id=='schedule')? true : false,
                        ],
						[
                            'label'=>Yii::t('backend', 'Students'),
                            'icon'=>'<i class="fa fa-lg fa-fw fa-child"></i>',
							'url'=>['/student/index','StudentSearch[showAllStudents]' => false],
                            'visible'=>Yii::$app->user->can('staffmember'),
                            'active'=>(Yii::$app->controller->id=='student')? true : false,
                        ],
						[
                            'label'=>Yii::t('backend', 'Customers'),
                            'icon'=>'<i class="fa fa-lg fa-fw fa-male"></i>',
                            'url'=>['/user/index', 'UserSearch[role_name]' => User::ROLE_CUSTOMER],
                            'visible'=>Yii::$app->user->can('staffmember'),
                            'active'=>(isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name']== User::ROLE_CUSTOMER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name']== User::ROLE_CUSTOMER)) ? true : false,
                        ],
						[
                            'label'=>Yii::t('backend', 'Teachers'),
                            'icon'=>'<i class="fa fa-graduation-cap"></i>',
                            'url'=>['/user/index', 'UserSearch[role_name]' => User::ROLE_TEACHER],	
                            'visible'=>Yii::$app->user->can('staffmember'),
                            'active'=>(isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name']== User::ROLE_TEACHER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name']== User::ROLE_TEACHER)) ? true : false,
                        ],
						[
                            'label'=>Yii::t('backend', 'Staff Members'),
                            'icon'=>'<i class="fa fa-users"></i>',
							'url'=>['/user/index', 'UserSearch[role_name]' => User::ROLE_STAFFMEMBER],    
                            'visible'=>Yii::$app->user->can('staffmember'),
                            'active'=>(isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name']== User::ROLE_STAFFMEMBER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name']== User::ROLE_STAFFMEMBER)) ? true : false,
                        ],
						[
                            'label'=>Yii::t('backend', 'Owners'),
                            'icon'=>'<i class="fa fa-user"></i>',
							'url'=>['/user/index', 'UserSearch[role_name]' => User::ROLE_OWNER],    
                            'visible'=>Yii::$app->user->can('administrator'),
                            'active'=>(isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name']== User::ROLE_OWNER || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name']== User::ROLE_OWNER)) ? true : false,
                        ],
						[
                            'label'=>Yii::t('backend', 'Administrators'),
                            'icon'=>'<i class="fa fa-user-secret"></i>',
							'url'=>['/user/index', 'UserSearch[role_name]' => User::ROLE_ADMINISTRATOR],    
                            'visible'=>Yii::$app->user->can('administrator'),
                            'active'=>(isset(Yii::$app->request->queryParams['UserSearch']['role_name']) && Yii::$app->request->queryParams['UserSearch']['role_name']== User::ROLE_ADMINISTRATOR || (isset(Yii::$app->request->queryParams['User']['role_name']) && Yii::$app->request->queryParams['User']['role_name']== User::ROLE_ADMINISTRATOR)) ? true : false,
                        ],
						[
                            'label'=>Yii::t('backend', 'Programs'),
                            'icon'=>'<i class="fa fa-table"></i>',
                            'url'=>['/program/index','ProgramSearch[type]' => Program::TYPE_PRIVATE_PROGRAM],
                            'visible'=>Yii::$app->user->can('staffmember'),
                            'active'=>(Yii::$app->controller->id=='program')? true : false,
                        ],
						[
                            'label'=>Yii::t('backend', 'Group Courses'), 
							'url'=>['/group-course/index'], 
							'icon'=>'<i class="fa fa-book"></i>',
                            'visible'=>Yii::$app->user->can('staffmember'),
                            'active'=>(Yii::$app->controller->id=='group-course') ? true : false,
                        ],
						[
                            'label'=>Yii::t('backend', 'Lessons'),
                            'url' => '/lesson/index',
                            'icon'=>'<i class="fa fa-music"></i>',
                            'visible'=>Yii::$app->user->can('staffmember'),
                            'active'=>(Yii::$app->controller->id=='lesson') ? true : false,
						],
						[
                            'label'=>Yii::t('backend', 'Invoices'),
                            'icon'=>'<i class="fa  fa-dollar"></i>',
                            'url'=>['/invoice/index','InvoiceSearch[type]' => INVOICE::TYPE_INVOICE], 
                            'visible'=>Yii::$app->user->can('staffmember'),
                            'active'=>(Yii::$app->controller->id=='invoice')? true : false,
							
                        ],
                        [
                            'label'=>Yii::t('backend', 'Release Notes'),
                            'icon'=>'<i class="fa fa-sticky-note"></i>',
							'url'=>['/release-notes/index'],    
                            'visible'=>Yii::$app->user->can('administrator'),
                            'active'=>(Yii::$app->controller->id=='release-notes')? true : false,
                        ],
                        [
                            'label'=>Yii::t('backend', 'System'),
                            'options' => ['class' => 'header']
                        ],
                        [
                            'label'=>Yii::t('backend', 'Access Control'),
                            'url' => '#',
                            'icon'=>'<i class="fa fa-edit"></i>',
                            'visible'=>Yii::$app->user->can('administrator'),
                            'options'=>['class'=>'treeview'],
                            'items'=>[
                                ['label'=>Yii::t('backend', 'Roles'), 'url'=>['/admin/role'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
                                ['label'=>Yii::t('backend', 'Permissions'), 'url'=>['/admin/permission'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
                                ['label'=>Yii::t('backend', 'Assignments'), 'url'=>['/admin/assignment'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
                                ['label'=>Yii::t('backend', 'Routes'), 'url'=>['/admin/route'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
                                ['label'=>Yii::t('backend', 'Rules'), 'url'=>['/admin/rule'], 'icon'=>'<i class="fa fa-angle-double-right"></i>'],
                            ]
                        ],
                        [
                            'label'=>Yii::t('backend', 'Timeline'),
                            'icon'=>'<i class="fa fa-bar-chart-o"></i>',
                            'url'=>['/timeline-event/index'],
                            'badge'=> TimelineEvent::find()->today()->count(),
                            'badgeBgClass'=>'label-success',
                        ],
                        [
                            'label'=>Yii::t('backend', 'Setup'),
                            'url' => '#',
                            'icon'=>'<i class="fa fa-cogs"></i>',
                            'options'=>['class'=>'treeview'],
                            'items'=>[
								[
                            		'label'=>Yii::t('backend', 'Locations'),
                            		'icon'=>'<i class="fa  fa-map-marker"></i>',
                            		'url'=>['/location/index'],
                            		'visible'=>Yii::$app->user->can('staffmember')
                        		],
								[
                            		'label'=>Yii::t('backend', 'Holidays'),
                            		'icon'=>'<i class="fa fa-car"></i>',
                            		'url'=>['/holiday/index'],
                            		'visible'=>Yii::$app->user->can('staffmember')
                        		],
								[
                            		'label'=>Yii::t('backend', 'PD Days'),
                            		'icon'=>'<i class="fa fa-calendar-times-o"></i>',
                            		'url'=>['/professional-development-day/index'],
                            		'visible'=>Yii::$app->user->can('staffmember')
                        		],
								[
                            		'label'=>Yii::t('backend', 'Import'),
                            		'icon'=>'<i class="fa  fa-upload"></i>',
                            		'url'=>['/user/import'],
                            		'visible'=>Yii::$app->user->can('staffmember')
                        		],
								[
									'label' => Yii::t('backend', 'Cities'),
									'icon' => '<i class="fa fa-building"></i>',
									'url' => ['/city/index'],
									'visible' => Yii::$app->user->can('staffmember')
								],
								[
									'label' => Yii::t('backend', 'Provinces'),
									'icon' => '<i class="fa  fa-upload"></i>',
									'url' => ['/province/index'],
									'visible' => Yii::$app->user->can('staffmember')
								], 
                                [
									'label' => Yii::t('backend', 'Taxes'),
									'icon' => '<i class="fa  fa-cny"></i>',
									'url' => ['/tax-code/index'],
									'visible' => Yii::$app->user->can('staffmember')
								],
								[
									'label' => Yii::t('backend', 'Countries'),
									'icon' => '<i class="fa fa-globe"></i>',
									'url' => ['/country/index'],
									'visible' => Yii::$app->user->can('staffmember')
								],
                                [
									'label'=>Yii::t('backend', 'Key-Value Storage'),
									'url'=>['/key-storage/index'],
									'icon'=>'<i class="fa fa-angle-double-right"></i>',
									'visible'=>Yii::$app->user->can('administrator')
								],
                                [
									'label'=>Yii::t('backend', 'Cache'),
									'url'=>['/cache/index'],
									'icon'=>'<i class="fa fa-angle-double-right"></i>',
									'visible'=>Yii::$app->user->can('administrator')
								],
                                [
                                    'label'=>Yii::t('backend', 'System Information'),
                                    'url'=>['/system-information/index'],
                                    'icon'=>'<i class="fa fa-angle-double-right"></i>',
									'visible'=>Yii::$app->user->can('administrator')
                                ],
                                [
                                    'label'=>Yii::t('backend', 'Logs'),
                                    'url'=>['/log/index'],
                                    'icon'=>'<i class="fa fa-angle-double-right"></i>',
                                    'badge'=>\backend\models\SystemLog::find()->count(),
                                    'badgeBgClass'=>'label-danger',
									'visible'=>Yii::$app->user->can('administrator')
                                ],
                            ]
                        ]
                    ]
                ]) ?>
            </section>
            <!-- /.sidebar -->
        </aside>

        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    <div class="pull-left">
                        <?php echo $this->title ?>
                    </div>
                    <?php if (isset($this->params['subtitle'])): ?>
                        
                        <div class="pull-right">
                            <div class="dropdown">
                              <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                More
                                <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                <li><a href="#">Action</a></li>
                                <li><a href="#">Another action</a></li>
                                <li><a href="#">Something else here</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="#">Separated link</a></li>
                              </ul>
                            </div>
                        </div>
                        <div class="pull-right m-r-10">
                            <?php echo $this->params['subtitle']; ?>
                        </div>
                    <?php endif; ?>
                    <div class="clearfix"></div>
                </h1>

                <?php //echo Breadcrumbs::widget([
                    // 'tag'=>'ol',
                    // 'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                // ]) ?>
            </section>

            <!-- Main content -->
            <section class="content">
                <?php
                $latestNotes = $this->params['latestNotes'];
                $unReadNotes = $this->params['unReadNotes'];
                ?>
                <?php if($role === User::ROLE_ADMINISTRATOR || $role === User::ROLE_STAFFMEMBER || $role === User::ROLE_OWNER):?>
                    <?php if ( empty($unReadNotes) && ! empty($latestNotes)):?>
                       <?php Yii::$app->session->setFlash('alert', [
                            'options' => ['class' => 'alert alert-warning release-notes', 'data-id' => $latestNotes->id],
                            'body' => $latestNotes->notes
                        ]);
                        ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (Yii::$app->session->hasFlash('alert')):?>
                    <?php echo \yii\bootstrap\Alert::widget([
                        'body'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                        'options'=>ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                    ])?>
                <?php endif; ?>
                <?php echo $content ?>
            </section><!-- /.content -->
        </aside><!-- /.right-side -->
    </div><!-- ./wrapper -->

<?php $this->endContent(); ?>
