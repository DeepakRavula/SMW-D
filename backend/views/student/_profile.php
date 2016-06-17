<?php

use yii\helpers\Html;
?>
<div class="user-details-wrapper">
	<div class="col-md-12">
		<p class="users-name"><?php echo $model->first_name; ?> <?php echo $model->last_name; ?></p>
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Birth date">
		<i class="fa fa-birthday-cake detail-icon"></i> <?php echo (new \DateTime($model->birth_date))->format('d-m-Y'); ?>
	</div>
	<div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Customer">
		<i class="fa fa-user detail-icon"></i> <?php echo !empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null ?>
	</div>
	<div class="col-md-2">
		<i class="fa fa-map-marker"></i> <?php echo !empty($model->customer->primaryAddress->address) ? $model->customer->primaryAddress->address : null ?>
	</div>
	<div class="clearfix"></div>
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