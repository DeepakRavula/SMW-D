<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model common\models\ClassroomUnavailability */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="classroom-unavailability-form">

    <?php $form = ActiveForm::begin([
		'id' => 'classroom-unavailability-form',
	]); ?>
<div class="row p-10">
        <div class="col-lg-6">
    <?php echo $form->field($model, 'fromDate')->widget(DatePicker::classname(), [
                'options' => [
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
        <div class="col-lg-6">
    <?php echo $form->field($model, 'toDate')->widget(DatePicker::classname(), [
                'options' => [
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
        <div class="col-lg-10">
    <?php echo $form->field($model, 'reason')->textarea(['rows' => 6]) ?>
		</div>
	</div>

    <div class="form-group">
        <?php echo Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
