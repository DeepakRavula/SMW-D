<?php
use yii\helpers\Html;
use common\models\Invoice;
use common\models\Program;
use yii\widgets\ListView;
use yii\data\ArrayDataProvider;

$this->title = 'Delete Student'
?>

<?php if(! empty($model)):?>
<div class="row-fluid p-20">
	<div class="rowfluid">
		<p class="c-title m-0"> Customer Details & Billing Address </p>
		  <?= $this->render('_view-customer',[
			  'model' => $model,
			]); ?>
	</div>
	<div class="clearfix"></div>
	<div class="col-xs-6">
		<p class="c-title m-0"></i> Enrolments </p>
		  <?= $this->render('_view-enrolment',[
			  	'model' => $model,
				'enrolments' => $enrolments,
				'groupEnrolments' => $groupEnrolments
			]); ?>
	</div>
	<div class="col-xs-4">
		<p class="c-title m-0"></i> Unused Pro Forma Invoice Credits</p>
		  <?= $this->render('_view-pro-forma-invoice',[
			  	'model' => $model,
			]); ?>
	</div>
	<div class="col-xs-8">
		<div class="row-fluid">
		<p class="c-title m-0"></i> Pending Invoices</p>
		  <?= $this->render('_view-invoice',[
			  	'model' => $model,
			]); ?>
		</div>
	</div>
	<div class="col-md-12 action-btns">
<?= Html::a('Confirm', ['delete-student', 'studentId' => $model->id], [
		'class' => 'btn btn-danger',
		'data' => [
			'confirm' => 'Are you sure you want to delete this item?',
			'method' => 'post',
		],
]) ?>
<?= Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	?>
</div>
</div>
<?php endif;?>
