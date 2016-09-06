<?php
use yii\helpers\Html;
use common\models\Invoice;
?>
<?php if(! empty($enrolmentModel)):?>
<?php
$pendingInvoices = Invoice::find()
		->pendingInvoices($enrolmentId, $model)
		->all();
if( ! empty($pendingInvoices)){
	$pendingInvoiceTotal = 0;
	foreach($pendingInvoices as $pendingInvoice){
		$pendingInvoiceTotal += $pendingInvoice->total; 
	}
}
?>
<div class="smw-box col-md-3 m-l-10 m-b-20">
<h4>Student Name : <?= $enrolmentModel->student->fullName;?></h4>
<h4>Program Name : <?= $enrolmentModel->program->name;?></h4>
<h4>Pending Invoice Total : <?= ! empty($pendingInvoiceTotal) ? $pendingInvoiceTotal : 0;?></h4>
</div>
<div class="clearfix"></div>
<?php endif;?>
<?= Html::a('Delete', ['delete', 'id' => $model->id], [
		'class' => 'btn btn-danger',
		'data' => [
			'confirm' => 'Are you sure you want to delete this item?',
			'method' => 'post',
		],
]) ?>
<?= Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	?>
