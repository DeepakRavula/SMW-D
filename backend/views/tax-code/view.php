<?php

use yii\helpers\Html;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\TaxCode */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tax Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);
?>
<div class="tax-code-view">
<div class="user-details-wrapper">
	<div class="col-md-12">
		<p class="users-name"><?php echo $model->province->name; ?></p>
	</div>
	<div class="row-fluid">
		<div class="col-md-3 hand"  data-toggle="tooltip" data-placement="bottom" title="Tax Type">
			<?php echo $model->taxType->name; ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Province">
			<i class="fa fa-map-marker detail-icon"></i> <?php echo $model->province->name; ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Province Code">
			<?php echo $model->code; ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Start Date">
			<?php 
            $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->start_date);
            echo $startDate->format('d-m-Y'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="row-fluid m-t-10">
		<div class="col-md-2 hand"  data-toggle="tooltip" data-placement="bottom" title="Rate">
			 <?php echo $model->rate; ?>
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
