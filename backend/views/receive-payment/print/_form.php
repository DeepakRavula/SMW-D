<?php
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Location;
use common\models\User;
use yii\bootstrap\Html;

?>
<?php $lessonCount = $lessonLineItemsDataProvider->getCount(); ?>
<?php $invoiceCount = $invoiceLineItemsDataProvider->getCount(); ?>
<?php if ($lessonCount <= 0 && $invoiceCount<=0 && $model->amount>0) : ?>
<div class="text-center"><h2>You didn't select any lessons or invoices</h2><br/><h4>so we'll save this payment as credit to your customer account</h4> </div>
<?php else:?>
<?php if ($lessonCount > 0) : ?>
<div class = "row">
    <div class = "col-md-12">
<?= Html::label('Lessons', ['class' => 'admin-login']) ?>

    <?= $this->render('/receive-payment/_lesson-line-item', [
        'model' => $model,
        'isCreatePfi' => false,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
        'searchModel' => $searchModel
    ]);
    ?>
    </div>  
    </div>  
<?php endif; ?>

        <?php if ($invoiceCount > 0) : ?>
    <div class = "row">        
        <div class = "col-md-12">
        <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
        <?= $this->render('/receive-payment/_invoice-line-item', [
            'model' => $model,
            'isCreatePfi' => false,
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'searchModel' => $searchModel
        ]);
        ?>
        </div>
</div>    
    <?php endif; ?>
    <?php endif; ?>
    <script>
        $(document).ready(function () {
            var amountValue = '<?= $model->amount ?>';
            var amount  =   parseFloat(amountValue);
            var header = '<div class="row"> <div class="col-md-6"> <h4 class="m-0">Receipt</h4> </div> </div>'; 
            if(amount>0){
                header = '<div class="row"> <div class="col-md-6"> <h4 class="m-0">Receipt</h4> </div> <div class="col-md-6"> <h4 class="amount-needed pull-right">Amount Received $<span class="">'+ amount +'</span></h4> </div> </div>';   
            }
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#popup-modal').find('.modal-header').html(header);
        $('.modal-save').text('Print');
        $('.modal-back').hide();
        $('.select-on-check-all').prop('checked', true);
    });
    $(document).on("click", '.modal-save', function() {
        var url = '<?= Url::to(['print/receipt' ,'id' => $receiptModel->id,'paymentId' => $model->paymentId]); ?>';
        window.open(url,'_blank');
        return false;
    });
    </script>