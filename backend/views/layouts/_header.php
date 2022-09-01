<?php
/**
 * @var yii\web\View
 */
use backend\assets\BackendAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\models\Location;
use common\widgets\LocationDropdown;

$bundle = BackendAsset::register($this);
?>
<!-- header logo: style can be found in header.less -->
        <header class="main-header">
			 <a href="<?php echo Yii::getAlias('@frontendUrl') ?>" class=" logo">
			  <!-- mini logo for sidebar mini 50x50 pixels -->
			  <span class="logo-mini">SMW</span>
			  <!-- logo for regular state and mobile devices -->
			  <span class="logo-lg"><b>Arcadia</b>SMW</span>
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
                ?>
                        <div class="m-t-10 pull-left">
                            <?php $form = Html::beginForm(); ?>    
                            <div class="btn-group">
                                <button class="btn dropdown-toggle" data-toggle="dropdown"><?= Location::findOne(['slug' => Yii::$app->location])->name; ?> &nbsp;&nbsp;<span class="caret"></span></button>
                                <?= LocationDropdown::widget(); ?>
                            </div>
                            <?php Html::endForm() ?>
                        </div>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
						 <li class="notifications-menu" data-toggle="tooltip" data-original-title="Blog" data-placement="bottom">
                            <a href="<?php echo Url::to(['blog/list']) ?>">
                                <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                            </a>
                        </li>
                        <!-- FreshWidget disabled --> 
                        <!-- <li class="notifications-menu" data-toggle="tooltip" data-original-title="Help" data-placement="bottom">
                            <a href="" onclick="FreshWidget.show(); return false;">
                                <i class="fa fa-support"></i>
                            </a>
                        </li> -->
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
                                    <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>" class="img-circle" />
                                    <p>
                                        <?= Yii::$app->user->identity->userProfile->fullName ?> - <?= Yii::$app->user->identity->getRoleName(); ?>
                                        <small>Member since <?= date('Y M', Yii::$app->user->identity->created_at) ?>
					</small>
					<small> Version 2 </small>
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
									<div class="pull-left">
                                        <?php echo Html::a(Yii::t('backend', 'Profile'), Url::to(['user/view', 'UserSearch[role_name]' => $role, 'id' => Yii::$app->user->id]), ['class' => 'btn btn-default btn-flat', 'data-method' => 'post']) ?>
                                    </div>
                                    <div class="pull-right">
                                    <?php if (Yii::$app->user->can('staffmember') || Yii::$app->user->can('owner')):?>
                                        <?php echo Html::a(Yii::t('backend', 'Lock'), ['sign-in/lock'], ['class' => 'btn btn-default btn-flat', 'data-method' => 'post']) ?>
                                    <?php endif; ?>
                                        <?php echo Html::a(Yii::t('backend', 'Logout'), ['sign-in/logout'], ['class' => 'btn btn-default btn-flat', 'data-method' => 'post']) ?>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
<script>
$(document).ready(function(){
	$('.location').change(function(){
		$.ajax({
			type     :'POST',
			cache    : false,
			url  : '<?= Url::to(['/location/change-location']); ?>',
			data: {
				location_id: $('.location').val()
			},
			success  : function(response) {
				location.reload();
			}
		});	
		return false;
	});
});	
</script>
