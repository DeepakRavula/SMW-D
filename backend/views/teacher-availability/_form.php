<?php

use common\models\User;
use common\models\Location;
use common\models\TeacherAvailability;
use kartik\time\TimePicker;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TeacherAvailability */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="row-fluid">
<div class="teacher-availability-form">
<h4>Add Teacher's Availability Day and Time</h4>
    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>
    <div class="row">
	<div class="col-md-2">
		<?php echo $form->field($model, 'day')->dropdownList(TeacherAvailability::getWeekdaysList());?>
	</div>
	<div class="col-md-2">
		<?php echo $form->field($model, 'from_time')->widget(TimePicker::classname(), []);?>
	</div>
	<div class="col-md-2">
		<?php echo $form->field($model, 'to_time')->widget(TimePicker::classname(), []);?>
	</div>

	</div>
	<div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Add' : 'Edit', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>
<div class="clearfix"></div>