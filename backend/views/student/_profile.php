<?php

use yii\helpers\Html;

?>
<div class="user-details-wrapper">
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
		<a href="/user/view?UserSearch%5Brole_name%5D=customer&id=<?php echo $model->customer->id ?>">
		<i class="fa fa-user detail-icon"></i> <?php echo !empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null ?>
	</a>
	</div>
	<div class="clearfix"></div>
		<div class="col-xs-12"><?php if (!empty($model->notes)) :?>
			<h5><em><i class="fa fa-info-circle"></i> Notes:
				<?php echo !empty($model->notes) ? $model->notes : null; ?></em>
			</h5>
			<?php endif; ?>
		</div>
	<div class="student-view">
		<div class="col-md-12 action-btns">
			<?php echo Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
	    </div>
	    <div class="clearfix"></div>
	</div>
</div>