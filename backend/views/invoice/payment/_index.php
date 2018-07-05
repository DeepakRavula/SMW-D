<?php
use common\models\Payment;
use common\models\Invoice;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

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

 <?php Pjax::Begin(['id' => 'invoice-view-payment-tab', 'timeout' => 6000]); ?> 
<?php $boxTools = null;?>
<?php if (!$print): ?>
<?php $boxTools = $this->render('_button', [
    ]);?>
<?php $attributeAmount='amount';?>
<?php else : $attributeAmount='';?>
<?php endif; ?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => $boxTools,
        'title' => 'Payments',
        'withBorder' => true,
    ])
    ?>
<div>
    		<?=
        $this->render('/invoice/payment/_payment-list', [
            'model' => $model,
            'searchModel' => $searchModel,
            'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
        ]);
        ?>
    
</div>
<?php
    $amount = 0.00;
    if ($model->total > $model->invoicePaymentTotal) {
        $amount = $model->balance;
    }
    if (empty($amount)) {
        $amount = 0.00;
    }
?>
<?php if ((int) $model->type === Invoice::TYPE_INVOICE):?>
<div class="clearfix"></div>
<?php endif; ?>
<?php LteBox::end() ?>
<?php Pjax::end(); ?>
<script type="text/javascript">
    $(document).on('click', '#apply-credit-grid tr', function () {
		var selected = $(this).hasClass("apply-credit-row");
		$("#apply-credit-grid tr").removeClass("apply-credit-row");
		if(!selected) {
            $(this).addClass("apply-credit-row");
		}

        var amount = $(this).data('amount');
        var id = $(this).data('id');
        var amountNeeded = <?= $amount; ?>;
        if(amount > amountNeeded) {
            $('#apply-credit-payment').val((amountNeeded).toFixed(2));          
        } else {
            $('#apply-credit-payment').val((amount).toFixed(2));          
        }
        $('#payment-amountneeded').val((amountNeeded).toFixed(2));          
        $('#payment-credit').val((amount).toFixed(2));
        $('#payment-sourceid').val(id);
        $('#apply-credit-payment').attr('readonly', false);
        return false;
    });
</script>

