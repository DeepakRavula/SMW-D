<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="payments-form p-l-20">
    <?php $form = ActiveForm::begin(); ?>
 	<div class="row">
        <div class="col-xs-5">
        <?php echo $form->field($model, 'date')->widget(DatePicker::classname(), [
                'options' => [
                    'value' => Yii::$app->formatter->asDate($model->date),
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
	</div>
    <div class="row">
    <div class="col-md-12">
        <div class="pull-right">
       <?php echo Html::submitButton(Yii::t('backend', 'Pay Now'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
			<?php 
            if (!$model->isNewRecord) {
                echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-default']);
            }
        ?>
    </div>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
