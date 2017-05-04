<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Qualification;
?>
<div class="row-fluid m-t-20 p-10">
	<div>
		<h5 class="m-t-5"><i class="fa fa-graduation-cap"></i> Qualifications</h5>
	</div>
    <?php yii\widgets\Pjax::begin(['id' => 'qualification-grid']); ?>
	<div class="row p-10">
	<div class="col-xs-6">
		<div class="row-fluid">
			<h4 class="pull-left m-r-20">Private Programs </h4> 
			<a href="#" class="add-new-qualification text-add-new"><i class="fa fa-plus"></i></a>
		<div class="clearfix"></div>
		 <?php echo GridView::widget([
			 'id' => 'qualification-grid',
			'dataProvider' => $privateQualificationDataProvider,
			'tableOptions' => ['class' => 'table table-bordered'],
			'headerRowOptions' => ['class' => 'bg-light-gray'],
			'columns' => [
				'program.name',
				'rate:currency',
		],
	]); ?>	
		</div>
	</div>
	<div class="col-xs-6">
		<div class="row-fluid">
			<h4 class="pull-left m-r-20">Group Programs </h4> 
			<a href="#" class="add-new-group-qualification text-add-new"><i class="fa fa-plus"></i></a>
			<?php echo GridView::widget([
			 'id' => 'qualification-grid',
				'dataProvider' => $groupQualificationDataProvider,
				'tableOptions' => ['class' => 'table table-bordered'],
				'headerRowOptions' => ['class' => 'bg-light-gray'],
				'columns' => [
					'program.name',
					'rate:currency',
			],
			]); ?>	
		</div>
		
	</div>
	</div>
	<?php yii\widgets\Pjax::end(); ?>
	<div class="clearfix"></div>
</div>
<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Edit Qualification</h4>',
		'id'=>'qualification-edit-modal',
	]);?>
	<div id="qualification-edit-content"></div>
	<?php Modal::end();?>		
<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Add Private Qualification</h4>',
		'id'=>'private-qualification-modal',
	]);
	echo $this->render('/qualification/_form-private', [
		'model' => new Qualification(),
		'userModel' => $model, 
	]);
	Modal::end();?>	
	<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Add Group Qualification</h4>',
		'id'=>'group-qualification-modal',
	]);
	echo $this->render('/qualification/_form-group', [
		'model' => new Qualification(),
		'userModel' => $model, 
	]);
	 Modal::end();?>	
<script>
$(document).ready(function() {
	$(document).on('click', '.add-new-qualification', function (e) {
		$('#private-qualification-modal').modal('show');
		return false;
	});
	$(document).on('click', '.add-new-group-qualification', function (e) {
		$('#group-qualification-modal').modal('show');
		return false;
	});
	$(document).on("click", "#qualification-grid tbody > tr", function() {
		var qualificationId = $(this).data('key');	
		$.ajax({
			url    : '<?= Url::to(['qualification/update']); ?>?id=' + qualificationId,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('#qualification-edit-content').html(response.data);
					$('#qualification-edit-modal').modal('show');
				}
			}
		});
		return false;
	});
	$(document).on("click", '.qualification-cancel', function() {
		$('#qualification-edit-modal').modal('hide');
		$('#group-qualification-modal').modal('hide');
		$('#private-qualification-modal').modal('hide');
		return false;
	});
	$(document).on('beforeSubmit', '#qualification-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
                    $.pjax.reload({container: '#qualification-grid', timeout: 6000});
					$('#qualification-edit-modal').modal('hide');
				}else
				{
				 $('#qualification-form').yiiActiveForm('updateMessages', response.errors, true);
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#qualification-form-create', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
                    $.pjax.reload({container: '#qualification-grid', timeout: 6000});
					$('#group-qualification-modal').modal('hide');
					$('#private-qualification-modal').modal('hide');
				}else
				{
				 $('#qualification-form-create').yiiActiveForm('updateMessages', response.errors, true);
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#group-qualification-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
                    $.pjax.reload({container: '#qualification-grid', timeout: 6000});
					$('#group-qualification-modal').modal('hide');
					$('#private-qualification-modal').modal('hide');
				}else
				{
				 $('#qualification-form-create').yiiActiveForm('updateMessages', response.errors, true);
				}
			}
		});
		return false;
	});
	$(document).on('click', '#qualification-delete', function (e) {
		var qualificationId = $('#qualification-grid tbody > tr').data('key'); 
		$.ajax({
			url    : '<?= Url::to(['qualification/delete']); ?>?id=' + qualificationId,
			type   : 'post',
			success: function(response)
			{
				   console.log(response);
			   if(response.status)
			   {
					$.pjax.reload({container : '#qualification-grid', timeout : 6000});
					$('#qualification-edit-modal').modal('hide');
				} 
			}
			});
			return false;
	});
});
</script>