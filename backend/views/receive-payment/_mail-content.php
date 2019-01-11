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

<div>
    <h4>Thank you for your payment!</h4>
</div>
<div>
    <h4>This is to acknowledge the receipt of payment from <?= $customer->userProfile->firstname .' '.$customer->userProfile->lastname; ?> on <?= Yii::$app->formatter->asDate($payment->date); ?> 
    in the amount of <?= $model->amount; ?> via <?= $payment->paymentMethod->name; ?>. We have distributed it to the items below.
    </h4>
</div>

<?php $lessonCount = $lessonLineItemsDataProvider->getCount(); ?>
<?php $invoiceCount = $invoiceLineItemsDataProvider->getCount(); ?>
<?php $groupLessonsCount = !empty($groupLessonLineItemsDataProvider) ? $groupLessonLineItemsDataProvider->getCount() : 0; ?>

<?php if ($lessonCount <= 0 && $invoiceCount <= 0 && $groupLessonsCount <= 0) : ?>
    <div class="text-center"><h2>You didn't select any lessons or invoices</h2><br/><h4>so we'll save this payment as credit to your customer account</h4> </div>
<?php else:?>  
    <?php if ($lessonCount > 0) : ?>
        <div class="col-xs-10">
            <?= Html::label('Lessons', ['class' => 'admin-login']) ?>
            <?= $this->render('/receive-payment/print/_lesson-line-item', [
                'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
                'searchModel' => $searchModel,
            ]); ?>   
        </div>     
    <?php endif; ?>
    
    <?php if ($groupLessonsCount > 0) : ?>
        <div class="col-xs-10">
            <?= Html::label('Group Lessons', ['class' => 'admin-login']) ?>
            <?= $this->render('/receive-payment/print/_group-lesson-line-item', [
                'isCreatePfi' => false,
                'lessonLineItemsDataProvider' => $groupLessonLineItemsDataProvider,
            ]); ?>
        </div>
    <?php endif; ?>

    <?php if ($invoiceCount > 0) : ?>
        <div class = "col-xs-10">
            <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
            <?= $this->render('/receive-payment/print/_invoice-line-item', [
                'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                'searchModel' => $searchModel,
            ]); ?>
        </div>
    <?php endif; ?>

    <div class="col-xs-10">       
        <?= Html::label('Payments Used', ['class' => 'admin-login']) ?>
        <?= $this->render('/receive-payment/print/_credits-available', [
            'paymentLineItemsDataProvider' => $paymentsLineItemsDataProvider,
                'searchModel' => $searchModel,
        ]); ?>
    </div>
<?php endif; ?> 