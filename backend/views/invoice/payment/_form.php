<?php

use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;

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
 <?php $query = PaymentMethod::find()->andWhere(['active'=> PaymentMethod::STATUS_ACTIVE]);
 if (!($model->isCreditUsed() || $model->isCreditApplied())) : 
    $query->andWhere(['displayed' => 1]); 
        endif; 
     $query->orderBy(['sortOrder' => SORT_ASC]);
    $paymentMethods = $query->all();  ?>
    <div class="row">
	   <div class="col-md-7">
            <?php echo $form->field($model, 'date')->widget(DatePicker::classname(), [
                'options' => [
                    'id' => 'extra-lesson-date',
                    'value' => Yii::$app->formatter->asDate($model->date),
                    'disabled' => $model->isCreditUsed() ||
                    $model->isCreditApplied(),                   
                ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
		    'showOnFocus' =>false,
                    'format' => 'M d,yyyy',
                   
                ],
            ]);
            ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'amount')->textInput(['class' => 'right-align payment-amount form-control','readOnly' => $model->isCreditUsed() ||
                    $model->isCreditApplied(),
                'value' => round($model->amount,2),
            ]);?>
        </div>
   </div>
<div class="row">

        <div class="col-md-7">
        
            <?= $form->field($model, 'reference')->textInput(['readOnly' => $model->isCreditUsed() ||
$model->isCreditApplied()]);?>
        </div>
        <div class="col-md-5">
        <?php echo $form->field($model, 'payment_method_id')->dropDownList(
 ArrayHelper::map($paymentMethods, 'id', 'name'),['disabled' => $model->isCreditUsed() ||
$model->isCreditApplied()]);
            ?>
        </div>
        </div>
    </div>
   <div class="row">
       <div class="col-md-12">
           <?= $form->field($model, 'notes')->textArea(['class' => 'form-control'])->label('Notes'); ?>
       </div>
   </div>
	<?php ActiveForm::end(); ?>
</div>
