<?php

use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\PaymentMethod;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class=" p-10">
<?php $form = ActiveForm::begin([
    'id' => 'payment-edit-form',
        'action' => Url::to(['payment/update', 'id' => $model->id]),
    'enableClientValidation' => true
]); ?>
    <div id="payment-edit-spinner" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
   <div class="row">
	   <div class="col-md-5">
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
        <div class="col-md-4">
            <?= $form->field($model, 'amount')->textInput(['readOnly' => $model->isCreditUsed() ||
                    $model->isCreditApplied(),
                'value' => \Yii::$app->formatter->asDecimal($model->amount, 2)
            ]);?>
        </div>
        <?php if ($model->payment_method_id === PaymentMethod::TYPE_CHEQUE) : ?>
            <?php $label = 'Cheque Number'; ?>
        <?php else : ?>
            <?php $label = 'Reference'; ?>
        <?php endif; ?>
        <div class="col-md-3">
            <?= $form->field($model, 'reference')->textInput()->label($label); ?>
        </div>
   </div>   
    <div class="row">  
        <div class="clearfix"></div>
	   <div class="col-md-12">
           <div class="pull-right">
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default payment-cancel']);?>       
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
           </div>
    <?php if (!$model->isCreditUsed() && !$model->isCreditApplied()) : ?>
           <div class="pull-left">
		<?= Html::a(
                'Delete',
                [
            'delete', 'id' => $model->id
        ],
        [
            'id' => 'payment-delete-button',
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this payment?',
                'method' => 'post',
            ]
        ]
            ); ?>
           </div>
    <?php endif; ?> 
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>