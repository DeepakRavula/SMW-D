<?php
/**
 * @var yii\web\View
 */
use backend\assets\BackendAsset;
use common\models\timelineEvent\TimelineEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\models\Location;
use common\models\UserLocation;
use yii\web\JsExpression;

$bundle = BackendAsset::register($this);
?>
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
                if ($role !== User::ROLE_ADMINISTRATOR) {
                    $userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id]);
                    Yii::$app->session->set('location_id', $userLocation->location_id);
                }
                ?>
                    <?php if ($role === User::ROLE_ADMINISTRATOR):?>
                        <div class="pull-left">
                            <?php $form = Html::beginForm(); ?>                        
                                 <?= Html::dropDownList('location_id', null,
                                  ArrayHelper::map(Location::find()->all(), 'id', 'name'), ['class' => 'form-control', 'id' => 'location_id', 'options' => [Yii::$app->session->get('location_id') => ['Selected' => 'selected']], 'onChange' => new JsExpression(
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
                        <?php endif; ?>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
						 <li class="notifications-menu" data-toggle="tooltip" data-original-title="Blog" data-placement="bottom">
                            <a href="<?php echo Url::to(['blog/list']) ?>">
                                <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li class="notifications-menu" data-toggle="tooltip" data-original-title="Give a feedback" data-placement="bottom">
                            <a href="" onclick="FreshWidget.show(); return false;">
                                <i class="fa fa-comment"></i>
                            </a>
                        </li>
                        <li id="timeline-notifications" class="notifications-menu"  data-toggle="tooltip" data-original-title="Notifications" data-placement="bottom">
                            <a href="<?php echo Url::to(['timeline-event/index']) ?>">
                                <i class="fa fa-bell"></i>
                                <span class="label label-success">
                                    <?php echo TimelineEvent::find()
										->andWhere(['locationId' => Yii::$app->session->get('location_id')])
										->today()->count() ?>
                                </span>
                            </a>
                        </li>
                        <!-- Notifications: style can be found in dropdown.less -->
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="user-menu">
							<span class="glyphicon glyphicon-user"></span>
							<span><?php echo Yii::$app->user->identity->userProfile->fullName ?></span>
							<span
                        class="label label-<?= Yii::$app->user->identity->getRoleBootstrapClass() ?>"><?= Yii::$app->user->identity->getRoleName() ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header light-blue">
                                    <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>" class="img-circle" alt="User Image" />
                                    <p>
                                        <?= Yii::$app->user->identity->userProfile->fullName ?> - <?= Yii::$app->user->identity->getRoleName(); ?>
                                        <small>Member since <?= date('Y M', Yii::$app->user->identity->created_at) ?>
                                        </small>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <?php echo Html::a(Yii::t('backend', 'Logout'), ['sign-in/logout'], ['class' => 'btn btn-default btn-flat', 'data-method' => 'post']) ?>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>