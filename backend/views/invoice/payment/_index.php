<?php
use common\models\Payment;
use common\models\Lesson;
use common\models\Invoice;
use common\models\PaymentMethod;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;

?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Add Payment</h4>',
    'id' => 'payment-modal',
]);
echo $this->render('payment-method/_form', [
	'model' => new Payment(),
	'invoice' => $model,
]);
Modal::end(); ?>
<?= $this->render('payment-method/_apply-credit', [
	'invoice' => $model,
]);?>
<div style="margin-bottom: 10px">
<?= Html::a(Yii::t('backend', '<i class="fa fa-plus" aria-hidden="true"></i> Add'), ['#'], ['class' => 'btn btn-primary btn-sm m-r-10 add-payment']);?>
	<?= Html::a(Yii::t('backend', 'Apply Credit'), ['#'], ['class' => 'btn btn-primary btn-sm apply-credit']);?>
</div>
<?php
$columns = [
    'date:date',
    'paymentMethod.name',
    [
        'label' => 'Number',
        'value' => function ($data) {
            $lesson = Lesson::findOne(['id' => $data->reference]);
            $invoice = Invoice::findOne(['id' => $data->reference]);
            if (!$data->invoice->isInvoice() && $data->isCreditUsed()) {
                $number = $lesson->getLessonNumber();	
            } else if (!$data->invoice->isInvoice() && $data->isCreditApplied()) {
                $number = $invoice->getInvoiceNumber();
            } else if ($data->invoice->isInvoice() && $data->isCreditUsed()) {
                $number = $invoice->getInvoiceNumber();
            } else if ($data->invoice->isInvoice() && $data->isCreditApplied()) {
                $number = $lesson->getLessonNumber();
            } else {
                $number = $data->reference;
            }

            return $number;
        },
        ],
        [
            'attribute' => 'amount',
			'format' => 'currency',
        ],
    ]; ?>

<div>
	<?php yii\widgets\Pjax::begin([
		'id' => 'invoice-payment-listing',
		'timeout' => 6000,
	]) ?>
	<?= GridView::widget([
		'id' => 'payment-grid',
        'dataProvider' => $invoicePaymentsDataProvider,
        'columns' => $columns,
		'summary' => ''
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>	
</div>
<?php
	$amount = 0.0;
	if ($model->total > $model->invoicePaymentTotal) {
		$amount = $model->balance;
	}
?>
<?php if ((int) $model->type === Invoice::TYPE_INVOICE):?>
<div id="invoice-payment-detail" class="pull-right col-md-4  m-b-20">
<?php echo $this->render('_invoice-summary', [
        'model' => $model,
    ]) ?>
</div>
<div class="clearfix"></div>
<?php endif; ?>
<script type="text/javascript">
$(document).ready(function(){
  	$('td').click(function () {
        var amount = $(this).closest('tr').data('amount');
        var id = $(this).closest('tr').data('id');
        var type = $(this).closest('tr').data('source');    
        var amountNeeded = '<?= $amount; ?>';  
        if(amount > amountNeeded) {
            $('input[name="Payment[amount]"]').val(amountNeeded);          
        } else {
            $('input[name="Payment[amount]"]').val(amount);          
        }
        $('input[name="Payment[amountNeeded]"]').val(amountNeeded);          
        $('#payment-credit').val(amount);
		$('#payment-sourceid').val(id);
		$('#payment-sourcetype').val(type);
    });
	$(document).on('beforeSubmit', '#apply-credit-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: 'json',
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container : '#invoice-payment-listing', timeout : 4000});
					invoice.updateSummarySectionAndStatus();
					$('#credit-modal').modal('hide');
				}else
				{
				 $('#apply-credit-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
			});
			return false;
	});
});
</script>
