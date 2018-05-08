<?php

use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="row user-create-form">
<?php $form = ActiveForm::begin([
    'id' => 'payment-form',
    'action' => Url::to(['payment/invoice-payment', 'id' => $invoice->id]),
]); ?>
    <div id="payment-add-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <div class="row">
    <div class="col-md-7">
            <?php echo $form->field($model, 'date')->widget(DatePicker::classname(), [
                 'dateFormat' => 'php:M d, Y',
                 'clientOptions' => [
                     'defaultDate' => (new \DateTime($model->date))->format('M d, Y'),
                     'changeMonth' => true,
                     'yearRange' => '-10:+20',
                     'changeYear' => true,
                 ],
            ])->textInput()->label('Date');
            ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'amount')->textInput(['class' => 'right-align payment-amount form-control']);?>
        </div>
    </div>
    <div class="row">
    <div class="reference col-md-7">
        <?= $form->field($model, 'reference')->textInput()->label('Reference'); ?>
    </div>
        <div class="col-md-5">
    <?php echo $form->field($model, 'payment_method_id')->dropDownList(
 ArrayHelper::map(PaymentMethod::find()
                ->andWhere([
                    'active' => PaymentMethod::STATUS_ACTIVE,
                    'displayed' => 1,
                ])
                  ->orderBy(['sortOrder' => SORT_ASC])->all(), 'id', 'name')
);
            ?>
        </div>
    </div>
    <div id="add-payment-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <div class="row">
        <div class="col-md-12">
         <?= $form->field($model, 'notes')->textArea(['class' => 'form-control'])->label('Notes'); ?>
        </div>
    </div>
    <div class="row">
	   <div class="form-group pull-right">
           <?= Html::a('Cancel', '', ['class' => 'btn btn-default payment-cancel-btn']);?>
        <?= Html::submitButton(Yii::t('backend', 'Pay'), ['class' => 'btn btn-info create-payment', 'name' => 'button']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
</div>
<script>
    $(document).ready(function() {
	var now = new Date();
        var today = (now.getMonth() + 1)  + '/' + now.getDate() + '/' + now.getFullYear();
	var currentDate = moment(today).format('MMM D, YYYY');
        $('#payment-date').val(currentDate);
    });	
</script>