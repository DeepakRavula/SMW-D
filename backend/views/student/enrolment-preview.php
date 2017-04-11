<?php
use yii\helpers\Html;
use common\models\Invoice;
use common\models\Program;

?>
<?php if (!empty($model)):?>
<?php
$pendingInvoices = Invoice::find()
        ->pendingInvoices($model->id, $model->student)
        ->joinWith(['invoicePayments ip' => function ($query) {
            $query->where(['ip.id' => null]);
        }])
        ->where(['invoice.type' => Invoice::TYPE_INVOICE])
        ->sum('invoice.total');

$invoicePartialPayments = Invoice::find()
        ->pendingInvoices($model->id, $model->student)
        ->where(['invoice.type' => Invoice::TYPE_INVOICE])
        ->all();
if (!empty($invoicePartialPayments)) {
    $count = 0;
    foreach ($invoicePartialPayments as $invoicePartialPayment) {
        if ($invoicePartialPayment->invoiceBalance > 0) {
            $count += 1;
        }
    }
}
$invoiceCredits = Invoice::find()->alias('i')
        ->select(['i.id', 'i.date', 'SUM(i.balance) as credit'])
        ->pendingInvoices($model->id, $model->student)
        ->where(['i.type' => Invoice::TYPE_INVOICE])
        ->andWhere(['<', 'balance', 0])
    	->all();
if (!empty($invoiceCredits)) {
    $originalInvoiceCredit = null;
    foreach ($invoiceCredits as $invoiceCredit) {
        $originalInvoiceCredit += $invoiceCredit->credit;
    }
}

$proFormaInvoiceCredits = Invoice::find()
    ->select(['invoice.id', 'invoice.date', 'SUM(payment.amount) as credit'])
    ->pendingInvoices($model->id, $model->student)
    ->joinWith(['invoicePayments ip' => function ($query) {
        $query->joinWith('payment payment');
    }])
    ->where(['invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE])
    ->groupBy('invoice.id')
    ->all();

if (!empty($proFormaInvoiceCredits)) {
    $proFormaCredit = null;
    foreach ($proFormaInvoiceCredits as $proFormaInvoiceCredit) {
        $proFormaCredit += $proFormaInvoiceCredit->credit;
    }
}
?>
<div class="smw-box col-md-6 m-l-20 m-b-30">
<h4>Customer Name & Billing Address : 
<div class="row-fluid">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left"><?= $model->student->customer->publicidentity ?>
             <em>
                <small><?php echo !empty($model->student->customer->email) ? $model->student->customer->email : null ?></small>
            </em> 
        </p>
    </div>
    <div class="row-fluid">
		<div id="w3" class="list-view">
            <div data-key="351">
                <div class="address p-t-6 p-b-6 relative  col-md-6">
                    <div><?= Html::encode(!empty($model->student->customer->billingAddress->address) ? $model->student->customer->billingAddress->address : null) ?> </div>
                    <div><?= Html::encode(!empty($model->student->customer->billingAddress->city->name) ? $model->student->customer->billingAddress->city->name : null) ?> <?= Html::encode(!empty($model->student->customer->billingAddress->province->name) ? $model->student->customer->billingAddress->province->name : null) ?></div>
                    <div><?= Html::encode(!empty($model->student->customer->billingAddress->country->name) ? $model->student->customer->billingAddress->country->name : null) ?> <?= Html::encode(!empty($model->student->customer->billingAddress->postal_code) ? $model->student->customer->billingAddress->postal_code : null) ?></div>
                </div>
                <div class="address p-t-6 p-b-6 relative  col-md-6">
                    <div><?= Html::encode(!empty($model->student->customer->primaryPhoneNumber->number) ? (!empty($model->student->customer->primaryPhoneNumber->number) ? $model->student->customer->primaryPhoneNumber->label->name.' : ' : null).''.$model->student->customer->primaryPhoneNumber->number : null) ?> </div>
                </div>
            </div>
        </div>		
    </div>
</div>
<div class="clearfix"></div>
<h4>Student Name : <?= $model->student->fullName; ?></h4>
<h4>Program Name : <?= $model->program->name; ?></h4>
<h4>Teacher Name : <?= $model->course->teacher->publicIdentity; ?></h4>
<h4>Duration: 
<?= Yii::$app->formatter->asDate($model->course->startDate).' to '.Yii::$app->formatter->asDate($model->course->endDate); ?>
</h4>
<h4>Pending Invoice Total : <?= !empty($pendingInvoiceTotal) ? $pendingInvoiceTotal : 0; ?></h4>
<h4>Number Of Invoice Partial Payment : <?= !empty($count) ? $count : 0; ?></h4>
<h4>Unused Invoice Credit : <?= !empty($originalInvoiceCredit) ? abs($originalInvoiceCredit) : 0; ?></h4>
<?php if ($model->course->program->isPrivate()):?>
<h4>Unused Pro Forma Invoice Credit: <?= !empty($proFormaCredit) ? $proFormaCredit : 0; ?></h4>
<?php endif; ?>
</div>
<div class="clearfix"></div>
<div>
<?= Html::a('Confirm', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
]) ?>
<?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']); ?>
</div>
<?php endif; ?>
