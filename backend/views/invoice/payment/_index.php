<?php
use yii\helpers\Html;
use common\models\Payment;
use common\models\Invoice;
use common\models\PaymentMethod;
use yii\bootstrap\ButtonGroup;
use yii\helpers\Url;
use yii\grid\GridView;

?>
<style>
    #apply-credit-btn{
        border-bottom-left-radius: 3px;
        border-top-left-radius: 3px;
    }
    #cheque-btn{
        border-left:0px;
    }
    #amex-btn{
        border-bottom-right-radius:3px;
        border-top-right-radius:3px;
    }
    .kv-table-wrap{
        margin-bottom:0;
    }
    #payment-method-btn-section{
        margin-top:     10px;
    }
    #invoice-payment-listing{
        padding-left:15px;
        padding-right: 15px;
    }
</style>

<?php
$columns = [
    'date:date',
    'paymentMethod.name',
    [
        'label' => 'Number',
        'value' => function ($data) {
            if ($data->isCreditApplied() || $data->isCreditUsed()) {
                $invoice = Invoice::findOne(['id' => $data->reference]);
                $number = $invoice->getInvoiceNumber();
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
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>	
</div>
<div class="col-md-8 m-t-10">  
    <?php $buttons = [];
    ?>
    <?php foreach (PaymentMethod::find()
        ->where([
                'active' => PaymentMethod::STATUS_ACTIVE,
                'displayed' => 1,
            ])
        ->orderBy(['sortOrder' => SORT_ASC])->all() as $method):?>
        <?php if ((int) $model->type === Invoice::TYPE_PRO_FORMA_INVOICE):?>
            <?php if ($method->name === 'Apply Credit'):?>
                <?php continue; ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php 
        $paymentType = $method->name;
        if (in_array($method->id, [8, 9, 10, 11])) {
            $paymentType = 'Credit Card';
        }?>
        <?php $paymentType = str_replace(' ', '-', trim(strtolower($paymentType))); ?>
        <?php $buttons[] = [
                'label' => $method->name,
                'options' => [
                    'class' => 'btn btn-outline-info',
                    'id' => str_replace(' ', '-', trim(strtolower($method->name))).'-btn',
                    'data-payment-type' => $paymentType,
                    'data-payment-type-id' => $method->id,
                ],
        ]; ?>
    <?php endforeach; ?>

    <?php // a button group with items configuration
    echo ButtonGroup::widget([
        'buttons' => $buttons,
        'options' => [
            'id' => 'payment-method-btn-section',
            'class' => 'btn-group-horizontal p-l-10 m-t-20 m-b-20',
        ],
    ]); ?>

    <?php
        $amount = 0.0;
        if ($model->total > $model->invoicePaymentTotal) {
            $amount = $model->balance;
        }
    ?>

    <?php foreach (PaymentMethod::findAll([
                'active' => PaymentMethod::STATUS_ACTIVE,
                'displayed' => 1,
                'id' => [4, 5, 6, 7],
        ]) as $method):?>
        <div id="<?= str_replace(' ', '-', trim(strtolower($method->name))).'-section'; ?>" class="payment-method-section" style="display: none;">
            <?php echo $this->render('payment-method/_'.str_replace(' ', '-', trim(strtolower($method->name))), [
                    'model' => new Payment(),
                    'invoice' => $model,
                    'amount' => $amount,
            ]); ?>  
        </div>
    <?php endforeach; ?>

        <div id="credit-card-section" class="payment-method-section" style="display: none;">
            <?php echo $this->render('payment-method/_credit-card', [
                    'model' => new Payment(),
                    'invoice' => $model,
                    'amount' => $amount,
            ]); ?>  
        </div>
</div>

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
  $('#payment-method-btn-section').on('click', '.btn', function() {
	 $('.payment-method-section').hide();
	 $('#' + $(this).data('payment-type') + '-section').show();
	 $('.payment-method-id').val($(this).data('payment-type-id'));
     $('#payment-method-btn-section .btn').removeClass('active');
     $(this).addClass('active');
     if($(this).data('payment-type') == 'apply-credit'){
    	$('input[type="text"]').val('');
        $('#credit-modal').modal('show');
     }
  });
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
});
</script>
<script>
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
</script>
