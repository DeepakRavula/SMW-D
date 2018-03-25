<?php

use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use common\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class=" p-10">
<?php $form = ActiveForm::begin([
    'id' => 'modal-form',
    'action' => Url::to(['payment/update', 'id' => $model->id]),
    'enableClientValidation' => true
]); ?>

    <div class="row">
	   <div class="col-md-7">
            <?php echo $form->field($model, 'date')->widget(DatePicker::classname(), [
                'options' => [
                    'id' => 'extra-lesson-date',
                    'value' => Yii::$app->formatter->asDate((new \DateTime($model->date))->format('d-m-Y')),
                ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                ],
            ]);
            ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'amount')->textInput(['class' => 'right-align payment-amount form-control','readOnly' => $model->isCreditUsed() ||
                    $model->isCreditApplied(),
                'value' => \Yii::$app->formatter->asDecimal($model->amount, 2),
            ]);?>
        </div>
   </div>
<div class="row">
        <div class="col-md-12">
        <?php if ($model->payment_method_id === PaymentMethod::TYPE_CHEQUE) : ?>
            <?php $label = 'Cheque Number'; ?>
        <?php else : ?>
            <?php $label = 'Reference'; ?>
        <?php endif; ?>
        
            <?= $form->field($model, 'reference')->textInput()->label($label); ?>
        </div>
    </div>
   <div class="row">
       <div class="col-md-12">
           <?= $form->field($model, 'notes')->textArea(['class' => 'form-control'])->label('Notes'); ?>
       </div>
   </div>
	<?php ActiveForm::end(); ?>
</div>
