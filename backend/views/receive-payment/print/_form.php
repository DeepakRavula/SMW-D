<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Html;

?>

<div>
    <h4 class = "payment-receipt">Thank you for your payment!</h4>
</div>
<div>
    <h4 class = "payment-receipt">This is to acknowledge the receipt of payment from <?= $paymentModel->userProfile->firstname .' '.$paymentModel->userProfile->lastname; ?> on <?= Yii::$app->formatter->asDate($paymentModel->date); ?> 
in the amount of <?= $model->amount; ?> via <?= $paymentModel->paymentMethod->name; ?>. We have distributed it to the items below.</h4>
</div>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['email/receipt',  'PaymentForm[userId]' => $model->userId, 
            'PaymentForm[invoiceCredits]' => $model->invoiceCredits, 'PaymentForm[paymentCredits]' => $model->paymentCredits,
            'PaymentForm[invoicePayments]' => $model->invoicePayments, 'PaymentForm[lessonPayments]' => $model->lessonPayments, 
            'PaymentForm[groupLessonPayments]' => $model->groupLessonPayments, 'PaymentForm[amount]' => $model->amount, 
            'PaymentForm[paymentId]' => $model->paymentId])
    ]); ?>

    <?php $lessonCount = !empty($lessonLineItemsDataProvider) ? $lessonLineItemsDataProvider->getCount() : 0; ?>
    <?php $invoiceCount = !empty($invoiceLineItemsDataProvider) ? $invoiceLineItemsDataProvider->getCount() : 0; ?>
    <?php $groupLessonsCount = !empty($groupLessonLineItemsDataProvider) ? $groupLessonLineItemsDataProvider->getCount() : 0; ?>
    <?php if ($lessonCount <= 0 && $invoiceCount <= 0 && $groupLessonsCount <= 0) : ?>
        <div class="text-center"><h2>You didn't select any lessons or invoices</h2><br/><h4>so we'll save this payment as credit to your customer account</h4> </div>
    <?php else:?>
        <?php if ($lessonCount > 0) : ?>
            <div class = "row">
                <div class = "col-md-12">
                    <?= Html::label('Lessons', ['class' => 'admin-login']) ?>

                    <?= $this->render('/receive-payment/print/_lesson-line-item', [
                        'model' => $model,
                        'isCreatePfi' => false,
                        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                        'searchModel' => $searchModel
                    ]); ?>
                </div>  
            </div>  
        <?php endif; ?>

        <?php if ($invoiceCount > 0) : ?>
            <div class = "row">        
                <div class = "col-md-12">
                    <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
                    <?= $this->render('/receive-payment/print/_invoice-line-item', [
                        'model' => $model,
                        'isCreatePfi' => false,
                        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                        'searchModel' => $searchModel
                    ]); ?>
                </div>
            </div>    
        <?php endif; ?>

        <?php if ($groupLessonsCount > 0) : ?>
            <?= Html::label('Group Lessons', ['class' => 'admin-login']) ?>
            <?= $this->render('_group-lesson-line-item', [
                'model' => $model,
                'isCreatePfi' => false,
                'lessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider
            ]); ?>
        <?php endif; ?>
        <div class = "row">        
            <div class = "col-md-12">
                <?= Html::label('Payments Used', ['class' => 'admin-login']) ?>
                <?= $this->render('/receive-payment/print/_credits-available', [
                    'paymentLineItemsDataProvider' => $paymentsLineItemDataProvider,
                    'searchModel' => $searchModel,
                ]); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php ActiveForm::end(); ?>
    <?php 
        $url = Url::to(['print/receipt',  'PaymentForm[userId]' => $model->userId, 
            'PaymentForm[invoiceCredits]' => $model->invoiceCredits, 'PaymentForm[paymentCredits]' => $model->paymentCredits,
            'PaymentForm[invoicePayments]' => $model->invoicePayments, 'PaymentForm[lessonPayments]' => $model->lessonPayments, 
            'PaymentForm[groupLessonPayments]' => $model->groupLessonPayments, 'PaymentForm[amount]' => $model->amount, 
            'PaymentForm[paymentId]' => $model->paymentId]); 
    ?>

<script>
    $(document).ready(function () {
        var amountValue = '<?= $model->amount ?>';
        var amount  = parseFloat(amountValue);
        var header = '<div class="row"> <div class="col-md-6"> <h4 class="m-0">Payment Receipt</h4> </div> </div>'; 
        if (amount > 0) {
            header = '<div class="row"> <div class="col-md-6"> <h4 class="m-0">Payment Receipt</h4> </div> <div class="col-md-6"> <h4 class="amount-needed pull-right">Amount Received $<span class="">'+ amount +'</span></h4> </div> </div>';   
        }
        $('#popup-modal .modal-dialog').css({'width': '1100px'});
        $('#popup-modal').find('.modal-header').html(header);
        $('.modal-save').show();
        $('.modal-save').text('Email');
        $('.modal-save-all').text('Print');
        $('.modal-save-all').show();
        $('#modal-back').hide();
        $('.print-statement').hide();
    });

    $(document).off('click', '.modal-save-all').on('click', '.modal-save-all', function() {
        $('#popup-modal').modal('hide');
        var url = '<?= $url; ?>';
        window.open(url, '_blank');
        return false;
    });
</script>
