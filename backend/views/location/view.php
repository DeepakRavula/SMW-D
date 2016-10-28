<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);
?>
<div class="location-view">
<div class="user-details-wrapper">
	<div class="col-md-12">
		<p class="users-name"><?php echo $model->name; ?></p>
	</div>
	<div class="row-fluid">
		<div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Address">
			<i class="fa fa-arrows-h detail-icon"></i> <?php echo $model->address; ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="City name">
			<i class="fa fa-location-arrow detail-icon"></i> <?php echo $model->city->name; ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Province">
			<i class="fa fa-map-marker detail-icon"></i> <?php echo $model->province->name; ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Country">
			<i class="fa fa-flag detail-icon"></i> <?php echo $model->country->name; ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Postal code">
			<i class="fa fa-paper-plane-o detail-icon"></i> <?php echo $model->postal_code; ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="row-fluid m-t-10">
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Phone number">
			<i class="fa fa-phone detail-icon"></i> <?php echo $model->phone_number; ?>
		</div>
	</div>
	
	<div class="clearfix"></div>

	<!-- If admin show this -->
	<?php if ($lastRole->name === User::ROLE_ADMINISTRATOR): ?>
	<div class="student-view">
		<div class="col-md-12 action-btns">
			<?php echo Html::a('<i class="fa fa-pencil"></i>Edit', ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
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
	<?php endif; ?>
</div>
</div>
