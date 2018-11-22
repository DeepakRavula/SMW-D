<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use common\models\Classroom;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use common\models\TeacherAvailability;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="calendar-event-color-form">
        <?php 
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        if (empty($teacherAvailabilityModel->id)) {
            $id = false;
        } else {
            $id = $teacherAvailabilityModel->id;
        }
        ?>
        <?php $form       = ActiveForm::begin([
            'id' => 'teacher-availability-form',
            'action' => Url::to(['teacher-availability/modify',
                'teacherId' => $model->id, 'id' => $id]),
        ]); ?>
   <div class="row p-20">     
        <div class="col-md-6 form-group">
                <?= $form->field($roomModel, 'from_time')->widget(TimePicker::classname(), [
                    'options' => [
                        'id' => 'teacher-availability-from-time'
                    ]]); ?>
        </div>
        <div class="col-md-6 form-group">
                <?= $form->field($roomModel, 'to_time')->widget(TimePicker::classname(), ['options' => [
                        'id' => 'teacher-availability-to-time'
                    ]]); ?>
        </div>
        <div class="col-md-6 form-group">
            <?php echo $form->field($roomModel, 'day')->dropdownList(TeacherAvailability::getWeekdaysList(), ['prompt' => 'select day']) ?>
        </div>
        <div class="col-md-6 form-group">
            <?=
            $form->field($roomModel, 'classroomId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(
                    Classroom::find()->notDeleted()->andWhere(['locationId' => $locationId])->orderBy(['name' => SORT_ASC])->all(),
                        'id',
                    'name'
                ),
                'options' => ['placeholder' => 'Select Classroom', 'class' => 'form-control'],
            ]);
            ?>
        </div>
    </div>
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <?= Html::a('Cancel', null, ['id' => 'cancel', 'class' => 'btn btn-default']); ?>
            <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'button']) ?>
        </div>
       <div class="pull-left">
 <?php if (!empty($teacherAvailabilityModel->id)) : ?>
            <?= Html::a(
                '<i class="fa fa-close"></i> Delete',
                [
                    'teacher-availability/delete', 'id' => $teacherAvailabilityModel->id
                ],
                [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ]
                ]
            ); ?>
        <?php endif; ?>
        </div>
</div>
</div>
        <?php ActiveForm::end(); ?>
    
</div>
<script>
$(document).ready(function () {
    $('#cancel').click(function() {
        $('#teacher-availability-modal').modal('hide');
    });
});
</script>