<?php

/**
 * @var yii\web\View
 */
use backend\assets\BackendAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\Location;
use yii\widgets\ActiveForm;
use frontend\models\search\LocationScheduleSearch;

$bundle = BackendAsset::register($this);
?>
<!-- header logo: style can be found in header.less -->
<header class="main-header">
	<a href="<?php echo Yii::getAlias('@frontendUrl') ?>" class="logo">
		<img src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"/>        
	</a>
	<!-- Header Navbar: style can be found in header.less -->
	<nav class="navbar navbar-static-top" role="navigation">
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				<!-- User Account: style can be found in dropdown.less -->
				<?php if (!Yii::$app->user->isGuest) : ?>
					<li class="notifications-menu" data-toggle="tooltip" data-original-title="Schedule" data-placement="bottom">
						<a href="<?php echo Url::to(['/schedule/index']) ?>">Schedule</a>
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
								<div class="pull-right">
									<?php echo Html::a(Yii::t('backend', 'Logout'), ['/user/sign-in/logout'], ['class' => 'btn btn-default btn-flat', 'data-method' => 'post']) ?>
								</div>
							</li>
						</ul>
					</li>
				<?php else: ?>
					<?php if(Yii::$app->controller->id === 'daily-schedule') : ?>
					<div class="location-filter pull-left">
						<?php $searchModel = new LocationScheduleSearch(); ?>
						<?php $form = ActiveForm::begin([
							'id' => 'schedule-search',
							'action' => Url::to(['daily-schedule/index']),
							'method' => 'get',
						]); ?>                      
						<?= $form->field($searchModel, 'locationId')->dropDownList(
							ArrayHelper::map(Location::find()->all(), 'id', 'name'),
							['class' => 'form-control', 'id' => 'locationId'])->label(false);
						?>
    					<?php ActiveForm::end(); ?>
					</div>  
					<?php endif; ?>
					<li class="notifications-menu" data-toggle="tooltip" data-original-title="Login" data-placement="bottom">
						<a href="<?php echo Url::to(['/user/sign-in/login']) ?>">Login
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</nav>
</header>
