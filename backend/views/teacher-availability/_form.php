<?php

use common\models\User;
use common\models\Location;
use common\models\TeacherAvailability;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TeacherAvailability */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="teacher-availability-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'location_id')->dropDownList(ArrayHelper::map(Location::find()->all(), 'id', 'name')); ?>

    <?php echo $form->field($model, 'day')->dropdownList(TeacherAvailability::getWeekdaysList());?>

    <?php echo $form->field($model, 'from_time');?>

    <?php echo $form->field($model, 'to_time');?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
$('#teacheravailability-from_time').timepicker({
    'minTime': '9:00am',
    'maxTime': '8:30pm',
	'step' : 30,
    'showDuration': false
});
</script>
<script type="text/javascript">
$('#teacheravailability-to_time').timepicker({
    'minTime': '9:30am',
    'maxTime': '8:30pm',
	'step' : 30,
    'showDuration': false
});
</script>
