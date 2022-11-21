<?php

/**
 * @var yii\web\View
 */
use frontend\assets\FrontendAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use common\models\Location;
use common\widgets\LocationDropdown;

$bundle = FrontendAsset::register($this);
?>
<!-- header logo: style can be found in header.less -->
<header class="main-header">
	<?php if (!Yii::$app->user->isGuest) : ?>
		<a href="<?php echo Yii::getAlias('@frontendUrl') ?>" class="logo">
			 <span class="logo-mini">SMW</span>
			  <!-- logo for regular state and mobile devices -->
			  <span class="logo-lg"><b>Arcadia</b>SMW</span>      
		</a>
		<?php 
            $userId = Yii::$app->user->id;
            $roles = Yii::$app->authManager->getRolesByUser($userId);
            $role = end($roles);
        ?>
		<!-- Header Navbar: style can be found in header.less -->
	<?php if (in_array($role->name, [User::ROLE_TEACHER, User::ROLE_CUSTOMER])) : ?>
		<nav class="navbar navbar-static-top" role="navigation">
			<div class="navbar-custom-menu">
				<ul class="nav navbar-nav">
					<div class="m-t-10 pull-left">
					<?php $userId = Yii::$app->user->id;
        			$roles = Yii::$app->authManager->getRolesByUser($userId);
       				$role = end($roles);
       				if ($role->name == User::ROLE_TEACHER) { ?>
						<?php $form = Html::beginForm(); ?>    
							<div class="btn-group">
								<button class="btn dropdown-toggle" data-toggle="dropdown"><?= Location::findOne(['slug' => Yii::$app->location])->name; ?> &nbsp;&nbsp;<span class="caret"></span></button>
								<?= LocationDropdown::widget(); ?>
							</div>
						<?php Html::endForm() ?>
					   <?php } ?>
					</div>
					<!-- User Account: style can be found in dropdown.less -->
					<li class="notifications-menu" data-toggle="tooltip" data-original-title="Schedule" data-placement="bottom">
						<a href="<?php echo Url::to(['/schedule/index']) ?>">Schedule</a>
					</li>
					<li class="notifications-menu" data-toggle="tooltip" data-original-title="Lessons" data-placement="bottom">
						<a href="<?php echo Url::to(['/user/default/invoiced-lessons']) ?>">Invoiced Lessons</a>
					</li>
					<li class="dropdown user user-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" id="user-menu">
							<span class="glyphicon glyphicon-user"></span>
							<span><?php echo Yii::$app->user->identity->userProfile->fullName ?></span>
						</a>
						<ul class="dropdown-menu">
							<!-- User image -->
							<li class="user-header light-blue">
								<img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>" class="img-circle" alt="User Image" />
								<p>
									<small>Member since <?= date('Y M', Yii::$app->user->identity->created_at) ?>
									</small>
							</li>
							<!-- Menu Footer-->
							<li class="user-footer">
								<div class="pull-left">
									<?php echo Html::a(Yii::t('backend', 'Profile'), Url::to(['/user/default/update',]), ['class' => 'btn btn-default btn-flat']) ?>
								</div>
								<div class="pull-right">
									<?php echo Html::a(Yii::t('backend', 'Logout'), ['/user/sign-in/logout'], ['class' => 'btn btn-default btn-flat', 'data-method' => 'post']) ?>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
	<?php endif; ?>
	<?php endif; ?>
</header>
