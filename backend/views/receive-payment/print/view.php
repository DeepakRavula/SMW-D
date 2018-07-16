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
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>

<div class="row m-t-25">
    <div class="col-md-6">
    </div>    
    <?php
     $locationModel = Location::findOne(['slug' => \Yii::$app->location]);
   echo $this->render('/print/_receipt-header', [
       'userModel'=>$customer,
       'locationModel'=>$locationModel,
]);
   ?>
</div>
<?php $lessonCount = $lessonLineItemsDataProvider->getCount(); ?>
<?php $invoiceCount = $invoiceLineItemsDataProvider->getCount(); ?>
<?php if ($lessonCount <= 0 && $invoiceCount<=0) : ?>
  <div class="text-center"><h2>You didn't select any lessons or invoices</h2><br/><h4>so we'll save this payment as credit to your customer account</h4> </div>
  <?php else:?>
  <div class="row">
    <div class="col-md-12">
       
        <?php if ($lessonCount > 0) : ?>
        <div class = "row">
            <div class = "col-md-12">
                  <?= Html::label('Lessons', ['class' => 'admin-login']) ?>
            <?= $this->render('/receive-payment/print/_lesson-line-item', [
                'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                'searchModel' => $searchModel,
            ]); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
       
        <?php if ($invoiceCount > 0) : ?>
        <div class = "row">
            <div class = "col-md-12">
                <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
            <?= $this->render('/receive-payment/print/_invoice-line-item', [
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'searchModel' => $searchModel,
            ]); ?>
            </div>
            </div>
        <?php endif; ?>
    </div>
</div>
        <div class = "row">        
            <div class = "col-md-12">
                <?= Html::label('Payments Used', ['class' => 'admin-login']) ?>
                <?= $this->render('/receive-payment/print/_credits-available', [
                    'paymentLineItemsDataProvider' => $paymentLineItemsDataProvider,
                    'searchModel' => $searchModel,
                    'receiptModel'=>$receiptModel,
    ]);
    ?>
    
    </div>
</div>  
<?php endif; ?>
    


   <script>
        $(document).ready(function() {
            window.print();
        });
    </script>   