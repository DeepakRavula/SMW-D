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
<h4>Customer Name & Billing Address : 
<div class="row-fluid">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left"><?= $model->customer->publicidentity ?>
             <em>
                <small><?php echo !empty($customer->email) ? $customer->email : null ?></small>
            </em> 
        </p>
    </div>
    <div class="row-fluid">
		<div id="w3" class="list-view">
            <div data-key="351">
                <div class="address p-t-6 p-b-6 relative  col-md-6">
                    <div><?= Html::encode( ! empty($model->customer->billingAddress->address) ? $model->customer->billingAddress->address : null) ?> </div>
                    <div><?= Html::encode( ! empty($model->customer->billingAddress->city->name) ? $model->customer->billingAddress->city->name : null) ?> <?= Html::encode( ! empty($model->customer->billingAddress->province->name) ? $model->customer->billingAddress->province->name : null) ?></div>
                    <div><?= Html::encode( ! empty($model->customer->billingAddress->country->name) ? $model->customer->billingAddress->country->name : null) ?> <?= Html::encode( ! empty($model->customer->billingAddress->postal_code) ? $model->customer->billingAddress->postal_code : null) ?></div>
                </div>
                <div class="address p-t-6 p-b-6 relative  col-md-6">
                    <div><?= Html::encode( ! empty($model->customer->primaryPhoneNumber->number) ? ( ! empty($model->customer->primaryPhoneNumber->number) ? $model->customer->primaryPhoneNumber->label->name.' : ' : null). '' .$model->customer->primaryPhoneNumber->number : null) ?> </div>
                </div>
            </div>
        </div>		
    </div>
</div>
<div class="clearfix"></div>
<h4>Student Name : <?= $enrolmentModel->student->fullName;?></h4>
<h4>Program Name : <?= $enrolmentModel->program->name;?></h4>
<h4>Teacher Name : <?= $enrolmentModel->teacher->publicIdentity;?></h4>
<h4>Duration: 
<?php if((int) $programType === Program::TYPE_PRIVATE_PROGRAM):?>
<?= Yii::$app->formatter->asDate($enrolmentModel->commencement_date) . ' to ' . Yii::$app->formatter->asDate($enrolmentModel->renewal_date);?>
<?php else:?>
<?= Yii::$app->formatter->asDate($enrolmentModel->groupCourse->start_date) . ' to ' . Yii::$app->formatter->asDate($enrolmentModel->groupCourse->end_date);?>
<?php endif;?>
</h4>
<h4>Pending Invoice Total : <?= ! empty($pendingInvoiceTotal) ? $pendingInvoiceTotal : 0;?></h4>
<?php if((int) $programType === Program::TYPE_PRIVATE_PROGRAM):?>
<h4>Unused Pro Forma Invoice Credit: <?= ! empty($proFormaCredit) ? $proFormaCredit : 0;?></h4>
<?php endif;?>
</div>
<div class="clearfix"></div>
<div>
<?= Html::a('Delete', ['delete', 'id' => $model->id], [
		'class' => 'btn btn-danger',
		'data' => [
			'confirm' => 'Are you sure you want to delete this item?',
			'method' => 'post',
		],
]) ?>
<?= Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	?>
</div>
<?php endif;?>
