<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="payments-form p-l-20">
    <?php $form = ActiveForm::begin([
        'id' => 'invoice-detail-form',
        'action' => Url::to(['invoice/update', 'id' => $model->id]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true
    ]); ?>
 	<div class="row">
        <div class="col-xs-7">
        <?php echo $form->field($model, 'date')->widget(DatePicker::classname(), [
                'options' => [
                    'value' => Yii::$app->formatter->asDate($model->date),
                ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
		            'showOnFocus' =>false,
                    'format' => 'M d, yyyy',
                ],
            ]);
            ?>
        </div>
	</div>
    <div class="row">
    <div class="form-group pull-right">
    <?php echo Html::a('Cancel', '', ['class' => 'm-r-10 btn btn-default invoice-detail-cancel']) ?>
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'm-r-10 btn btn-info', 'name' => 'invoice-detail-button']) ?>
    </div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
