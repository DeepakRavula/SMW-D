<?php
use yii\helpers\Html;
use common\models\Invoice;
use common\models\Program;
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

$proFormaInvoiceCredits = Invoice::find()->alias('i')
	->select(['i.id', 'i.date', 'SUM(p.amount) as credit'])
	->joinWith(['lineItems li'=>function($query) use($enrolmentId, $model){
			$query->joinWith(['lesson l'=>function($query) use($enrolmentId, $model){	
				$query->joinWith(['enrolment e'=>function($query) use($enrolmentId, $model){
					$query->joinWith('student s')
						->where(['s.customer_id' => $model->customer->id, 's.id' => $model->id]);
					}])
					->where(['e.id' => $enrolmentId]);
				}]);
			}])
	->joinWith(['invoicePayments ip' => function($query) use($model){
		$query->joinWith(['payment p' => function($query) use($model){
		}]);
	}])
	->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE])
	->groupBy('i.id')
	->all();

if( ! empty($proFormaInvoiceCredits)){
	$proFormaCredit = null;
	foreach($proFormaInvoiceCredits as $proFormaInvoiceCredit){
		$proFormaCredit += $proFormaInvoiceCredit->credit; 
	}
}
?>
<div class="smw-box col-md-6 m-l-20 m-b-30">
<h4>Student Name : <?= $enrolmentModel->student->fullName;?></h4>
<h4>Program Name : <?= $enrolmentModel->program->name;?></h4>
<h4>Pending Invoice Total : <?= ! empty($pendingInvoiceTotal) ? $pendingInvoiceTotal : 0;?></h4>
<?php if((int) $programType === Program::TYPE_PRIVATE_PROGRAM):?>
<h4>Pro Forma Invoice Credit: <?= ! empty($proFormaCredit) ? $proFormaCredit : 0;?></h4>
<?php endif;?>
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
