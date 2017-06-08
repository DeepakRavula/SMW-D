<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use common\models\User;
?>
<div class="student-profile user-details-wrapper">
	<div class="row">
		<div class="col-md-12">
			<p class="users-name"><?php echo $model->first_name; ?> <?php echo $model->last_name; ?></p>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Birth date">
			<i class="fa fa-birthday-cake detail-icon"></i> <?php echo !empty($model->birth_date) ? Yii::$app->formatter->asDate($model->birth_date) : null; ?>
		</div>
		<?php if (! empty($model->birth_date)) : ?>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Age">
			<i class="fa fa-birthday-cake detail-icon"></i> <?php
			$birthDate = new DateTime($model->birth_date);
			$currentDate   = new DateTime('today');
			echo $birthDate->diff($currentDate)->y .'yrs old';
			?>
		</div>
		<?php endif;?>
		<div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Customer">
			<a href="<?= Url::to(['/user/view','UserSearch[role_name]' => User::ROLE_CUSTOMER,'id' => $model->customer->id]); ?>">
			<i class="fa fa-user detail-icon"></i> <?php echo !empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null ?>
		</a>
		</div>
		<div class="clearfix"></div>
		<div class="student-view">
			<div class="col-md-12 action-btns">
				<?php echo Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => 'm-r-20 student-profile-edit-button']) ?>
		    </div>
		    <div class="clearfix"></div>
		</div>		
	</div>
</div>
<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Edit Student Profile</h4>',
		'id'=>'student-profile-modal',
	]);
	echo $this->render('_form', [
		'model' => $model,
		'customer' => $model->customer
	]);
	Modal::end();
	?>
