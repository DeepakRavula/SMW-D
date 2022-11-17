<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\color\ColorInput;

/* @var $this yii\web\View */
/* @var $model common\models\CalendarEventColor */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="calendar-event-color-form">
	<div class="p-10">
    <?php $form = ActiveForm::begin(); ?>
	<?php foreach ($eventModels as $index => $eventModel): ?>
	<?php
        // necessary for update action.
        if (!$eventModel->isNewRecord) {
            echo Html::activeHiddenInput($eventModel, "[{$index}]id");
        }
    ?>
	<div class="form-group col-lg-6">
	<?php echo $form->field($eventModel, "[{$index}]name")->textInput(['readonly' => true])->label(false); ?>
	</div>
	<div class="form-group col-lg-6">
    <?php echo $form->field($eventModel, "[{$index}]code")->widget(ColorInput::classname(), [
        'options' => ['placeholder' => 'Select color ...'],
    ])->label(false);
    ?>
	</div>
	<?php endforeach; ?>
	<div class="form-group pull-right">
       <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    </div>

</div>
