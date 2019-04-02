<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="blog-form">
<?php 
        $url = Url::to(['blog/update', 'id' => $model->id]);
    if ($model->isNewRecord) {
        $url = Url::to(['customer-recurring-payment-enrolment/create', 'id' => $model->id]);
    }
        $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
    ]); ?>
    <?php $paymentMethods = PaymentMethod::find()
        ->andWhere(['active'=> PaymentMethod::STATUS_ACTIVE])
        ->andWhere(['displayed' => 1])
        ->orderBy(['sortOrder' => SORT_ASC])
        ->all(); ?>
    <div class="row">
	<div class="col-md-4 ">
        <?php $day= ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', 
        '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', 
        '17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22', '23' => '23', '24' => '24', 
        '25' => '25', '26' => '26', '27' => '27', '28' => '28']; ?>
    	<?= $form->field($model, 'entryDay')->dropDownList($day, ['prompt'=>'Choose a Day'])?>
    </div>
    <div class="col-md-4 ">
    	<?= $form->field($model, 'paymentDay')->dropDownList($day, ['prompt'=>'Choose a Day'])?>
    </div>
    <div class="col-md-4 ">
    <?php $frequency= ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', 
        '9' => '9', '10' => '10', '11' => '11', '12' => '12']; ?>
    <?= $form->field($model, 'paymentFrequencyId')->dropDownList($frequency, ['prompt'=>'Choose a Frequency'])?>    
    </div>
    <div class="col-md-4 ">
    <?= $form->field($model, 'paymentMethodId')->dropDownList(ArrayHelper::map($paymentMethods, 'id', 'name'))
                ->label('Payment Method'); ?>    
    </div>
    <div class="col-md-4 ">
    <?= $form->field($model, 'expiryDate')->widget(DatePicker::className(), [
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                'changeMonth' => true,
                'yearRange' => '-70:+0',
                'changeYear' => true,
                ], ])->textInput(['placeholder' => 'Select Expiry Date']);?>    
    </div>
    <div class="col-md-4 ">
    <?= $form->field($model, 'amount')->textInput(); ?>    
    </div>
    </div>
</div>
    <?php ActiveForm::end(); ?>
<script>
    $(document).on('modal-success', function(event, params) {
        $.pjax.reload({url: url, container: "#recurring-payment-list", replace: false, timeout: 4000});
        return false;
    });
    
    $(document).on('modal-delete', function(event, params) {
        $.pjax.reload({url: url, container: "#recurring-payment-list", replace: false, timeout: 4000});
        return false;
    });
</script>