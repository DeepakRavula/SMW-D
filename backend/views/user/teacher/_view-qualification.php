<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
?>
<div class="row-fluid m-t-20 p-10">
	<div>
		<h5 class="m-t-5"><i class="fa fa-graduation-cap"></i> Qualifications</h5>
	</div>
    <?php yii\widgets\Pjax::begin(['id' => 'qualification-grid']); ?>
	<div class="row p-10">
	<div class="col-xs-6">
		<div class="row-fluid">
		<p class="c-title m-0 p-10"><i class="fa fa-music"></i> Private Programs </p>
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
			<p class="c-title m-0 p-10 "><i class="fa fa-music"></i> Group Programs</p>
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
<script>
$(document).ready(function() {
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