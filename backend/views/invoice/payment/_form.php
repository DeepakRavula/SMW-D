<?php

use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class=" p-10">
<?php $form = ActiveForm::begin([
    'id' => 'payment-edit-form',
	'action' => Url::to(['payment/edit', 'id' => $model->id]),
	'enableAjaxValidation' => true,
	'enableClientValidation' => false
]); ?>
   <div class="row">
	   <div class="col-md-6">
            <?php echo $form->field($model, 'date')->widget(DatePicker::classname(), [
                'options' => [
                    'id' => 'extra-lesson-date',
                    'value' =>Yii::$app->formatter->asDate((new \DateTime())->format('d-m-Y')),
                ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'amount')->textInput();?>
        </div>
        <div class="clearfix"></div>
	   <div class="col-md-6 form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default payment-cancel']);?>
		<?= Html::a('Delete', [
            'delete', 'id' => $model->id
        ],
        [
			'id' => 'payment-delete-button',
            'class' => 'btn btn-primary',
            'data' => [
                'confirm' => 'Are you sure you want to delete this payment?',
                'method' => 'post',
            ]
        ]); ?>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>