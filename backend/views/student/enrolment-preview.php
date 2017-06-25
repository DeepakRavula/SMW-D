<?php
use yii\helpers\Html;
use common\models\Invoice;
use common\models\Program;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

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
<table class="table">
    <tr>
        <td>Customer Name : </td>
        <td><?= $model->student->customer->publicidentity ?></td>
    </tr>
    <tr>
        <td>Email:</td>
        <td><?php echo !empty($model->student->customer->email) ? $model->student->customer->email : null ?></td>
    </tr>
    <tr>
        <td>Billing Address :</td>
        <td>
            <div class="address">
                <div><?= Html::encode(!empty($model->student->customer->billingAddress->address) ? $model->student->customer->billingAddress->address : null) ?> </div>
                <div><?= Html::encode(!empty($model->student->customer->billingAddress->city->name) ? $model->student->customer->billingAddress->city->name : null) ?> <?= Html::encode(!empty($model->student->customer->billingAddress->province->name) ? $model->student->customer->billingAddress->province->name : null) ?></div>
                <div><?= Html::encode(!empty($model->student->customer->billingAddress->country->name) ? $model->student->customer->billingAddress->country->name : null) ?> <?= Html::encode(!empty($model->student->customer->billingAddress->postal_code) ? $model->student->customer->billingAddress->postal_code : null) ?></div>
            </div>
            <div class="address">
                <div><?= Html::encode(!empty($model->student->customer->primaryPhoneNumber->number) ? (!empty($model->student->customer->primaryPhoneNumber->number) ? $model->student->customer->primaryPhoneNumber->label->name.' : ' : null).''.$model->student->customer->primaryPhoneNumber->number : null) ?> </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>Student Name :</td>
        <td><?= $model->student->fullName; ?></td>
    </tr>
    <tr>
        <td>Program Name :</td>
        <td><?= $model->program->name; ?></td>
    </tr>
    <tr>
        <td>Teacher Name :</td>
        <td><?= $model->course->teacher->publicIdentity; ?></td>
    </tr>
    <tr>
        <td>Duration: </td>
        <td><?= Yii::$app->formatter->asDate($model->course->startDate).' to '.Yii::$app->formatter->asDate($model->course->endDate); ?></td>
    </tr>
    <tr>
        <td>Pending Invoice Total : </td>
        <td><?= !empty($pendingInvoiceTotal) ? $pendingInvoiceTotal : 0; ?></td>
    </tr>
    <tr>
        <td>Number Of Invoice Partial Payment :</td>
        <td><?= !empty($count) ? $count : 0; ?></td>
    </tr>
    <tr>
        <td>Unused Invoice Credit :</td>
        <td> <?= !empty($originalInvoiceCredit) ? abs($originalInvoiceCredit) : 0; ?></td>
    </tr>
    <?php if ($model->course->program->isPrivate()):?>
    <tr>
        <td>
             Unused Pro Forma Invoice Credit : 
        </td>
        <td>
            <?= !empty($proFormaCredit) ? $proFormaCredit : 0; ?>
        </td>
    </tr>
    <?php endif; ?>
</table>
<?php $form = ActiveForm::begin([
	'id' => 'enrolment-delete-form',
	'action' => Url::to(['enrolment/delete', 'id' => $model->id])
]); ?>
<div>
<?= Html::a('Delete','#', [
        'class' => 'btn btn-danger enrolment-delete-button',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
]) ?>
<?= Html::a('Cancel','#', ['class' => 'btn enrolment-delete-cancel-button']); ?>
</div>
<?php ActiveForm::end(); ?>
<?php endif; ?>
