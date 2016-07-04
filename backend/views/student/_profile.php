<?php

use yii\helpers\Html;
?>
<div class="user-details-wrapper">
	<div class="col-md-12">
		<p class="users-name"><?php echo $model->first_name; ?> <?php echo $model->last_name; ?></p>
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Birth date">
		<i class="fa fa-birthday-cake detail-icon"></i> <?php echo Yii::$app->formatter->asDate($model->birth_date); ?>
	</div>
	<div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Customer">
		<a href="/user/view?UserSearch%5Brole_name%5D=customer&id=<?php echo $model->customer->id ?>">
		<i class="fa fa-user detail-icon"></i> <?php echo !empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null ?>
	</a>
	</div>
	<div class="clearfix"></div>
		<div class="row-fluid"><?php if(! empty($model->notes)) :?>
			<h5 class="m-0"><em><i class="fa fa-info-circle"></i> Notes:
				<?php echo ! empty($model->notes) ? $model->notes : null; ?></em>
			</h5>
			<?php endif;?>
		</div>
	<div class="student-view">
		<div class="col-md-12 action-btns">
			<?php echo Html::a('<i class="fa fa-pencil"></i> Update details', ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
			<?php
			echo Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->id], [
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				],
			])
			?>
	    </div>
	    <div class="clearfix"></div>
	</div>
</div>