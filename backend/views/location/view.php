<?php

use yii\helpers\Html;
use common\models\User;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

use kartik\date\DatePickerAsset;
DatePickerAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = $model->name;
$this->params['action-button'] = Html::a('<i class="fa fa-pencil"></i> Edit', '#', ['class' => 'btn btn-primary btn-sm edit-location']); 
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);
?>
<?php Pjax::begin([
	'id' => 'location-view']) ; ?>
<div class="row">
	<div class="col-md-6">	
		<?php
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'title' => 'Details',
			'withBorder' => true,
		])
		?>
		<dl class="dl-horizontal">
			<dt>Email</dt>
			<dd><?= $model->email; ?></dd>
			<dt>Phone</dt>
			<dd><?= !empty($model->phone_number) ? $model->phone_number : null; ?></dd>
			<dt>Royalty</dt>
			<dd><?= !empty($model->royalty->value) ? $model->royalty->value . '%' : null; ?></dd>
			<dt>Advertisement</dt>
			<dd><?= !empty($model->advertisement->value) ?  $model->advertisement->value . '%' : null; ?></dd>
			<dt>Conversion Date</dt>
			<dd><?= !empty($model->conversionDate) ?  Yii::$app->formatter->asDate($model->conversionDate) : null; ?></dd>
		</dl>
		<?php LteBox::end() ?>
		</div> 
	<div class="col-md-6">	
		<?php
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'title' => 'Address',
			'withBorder' => true,
		])
		?>
		<dl class="dl-horizontal">
			<dt>Address</dt>
			<dd><?= $model->address; ?></dd>
			<dt>City</dt>
			<dd><?= $model->city->name; ?></dd>
			<dt>Province</dt>
			<dd><?= $model->province->name; ?></dd>
			<dt>Country</dt>
			<dd><?= $model->country->name; ?></dd>
			<dt>Postal</dt>
			<dd><?= $model->postal_code; ?></dd>
		</dl>
		<?php LteBox::end() ?>
		</div> 
</div>
<?php Pjax::end(); ?>
<?php Modal::begin([
        'header' => '<h4 class="m-0">Location</h4>',
        'id' => 'location-edit-modal',
    ]); ?>
<div id="location-edit-content"></div>
 <?php  Modal::end(); ?>
<script>
	$(document).ready(function(){
		$(document).on('click', '.edit-location', function () {
			var locationId = '<?= $model->id;?>';
		$.ajax({
			url    : '<?= Url::to(['location/update']); ?>?id=' + locationId,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
				if(response.status)
				{
					$('#location-edit-content').html(response.data);
					$('#location-edit-modal').modal('show');
				}
			}
		});
		return false;
	});	
	$(document).on('click', '.location-cancel', function () {
		$('#location-edit-modal').modal('hide');
		return false;
	});
	});
</script>