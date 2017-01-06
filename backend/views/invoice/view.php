<?php

use yii\helpers\Html;
use backend\models\search\InvoiceSearch;
use yii\bootstrap\Tabs;
use yii\widgets\ActiveForm;
use common\models\InvoiceLineItem;
use kartik\switchinput\SwitchInput;
use common\models\Note;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = (int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE ? 'Pro-forma Invoice' : 'Invoice';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'InvoiceSearch[type]' => $model->type], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>
<style>
  .invoice-view .logo>img{
    width: 216px;
  }
    table>thead>tr>th:first-child,
    table>tbody>tr>td:first-child{
        text-align: left !important;
    }
    table>thead>tr>th:last-child,
    table>tbody>tr>td:last-child{
      text-align: right;
    }
    .badge{
      border-radius: 50px;
      font-size: 18px;
      font-weight: 400;
      padding: 5px 15px;
    }
    .smw-search{
      left: 170px;
    }
</style>
<?php 
$lineItem = InvoiceLineItem::findOne(['invoice_id' => $model->id]);
if (!empty($lineItem)) {
    $itemTypeId = $lineItem->item_type_id;
} else {
    $itemTypeId = null;
}

?>
<div class="invoice-index p-10">
		<?= Html::a('<i class="fa fa-envelope-o"></i> Mail this Invoice', ['send-mail', 'id' => $model->id], ['class' => 'btn btn-default pull-right  m-l-20']) ?>
        <?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default pull-right m-l-20', 'target' => '_blank']) ?>
<?php $form = ActiveForm::begin([
                'id' => 'mail-flag',
            ]);?>
		<?php if ((int) $model->type === InvoiceSearch::TYPE_PRO_FORMA_INVOICE): ?>
			<?=
			Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->id],
				[
				'class' => 'btn btn-default pull-right',
				'id' => 'delete-button',
			])
			?>
			<div class='mail-flag'>
			<?=
            $form->field($model, 'isSent')->widget(SwitchInput::classname(),
                [
                'name' => 'isSent',
                'pluginOptions' => [
                    'handleWidth' => 60,
                    'onText' => 'Sent',
                    'offText' => 'Not Sent',
                ],
            ])->label(false);
            ?>
			</div>
		<?php endif; ?>
    <?php ActiveForm::end(); ?>
</div>
<?php if(empty($model->lineItem) || $model->lineItem->isMisc()) : ?>
<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$customerContent = $this->render('_customer', [
    'model' => $model,
    'customer' => $customer,
]);
$guestContent = $this->render('_guest', [
    'model' => $model,
    'userModel' => $userModel,
    'customer' => $customer,
]);
?>
<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Customer',
            'content' => $customerContent,
            'options' => [
                    'id' => 'customer-tab',
                ],
        ],
        [
            'label' => 'Walk-in',
            'content' => $guestContent,
            'options' => [
                    'id' => 'guest-tab',
                ],
        ],
    ],
]); ?>
</div>
</div>
<?php endif; ?>
<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$invoiceContent = $this->render('_view-invoice', [
    'model' => $model,
    'customer' => $customer,
    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
]);
$paymentContent = $this->render('_payment', [
    'model' => $model,
    'invoicePayments' => $invoicePayments,
    'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
]);
$noteContent = $this->render('note/view', [
	'model' => new Note(),
	'noteDataProvider' => $noteDataProvider
]);
?>
<?php echo Tabs::widget([
    'items' => [
        [
            'label' => 'Details',
            'content' => $invoiceContent,
            'options' => [
                    'id' => 'invoice',
                ],
        ],
		[
            'label' => 'Notes',
            'content' => $noteContent,
            'options' => [
                'id' => 'note',
            ],
        ],
        [
            'label' => 'Payments',
            'content' => $paymentContent,
            'options' => [
                    'id' => 'payment',
                ],
        ],
    ],
]); ?>
</div>
</div>
<script>
 $(document).ready(function() {
	 $(document).on('click', '#invoice-note', function (e) {
		$('#invoice-note-modal').modal('show');
		return false;
  	});
	$(document).on('beforeSubmit', '#invoice-note-form', function (e) {
		$.ajax({
			url    : '<?= Url::to(['note/create', 'instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_INVOICE]); ?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container : '#invoice-note-listing', timeout : 12000});
					$('#invoice-note-modal').modal('hide');
				}else
				{
				 $('#invoice-note-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});
	});
</script>