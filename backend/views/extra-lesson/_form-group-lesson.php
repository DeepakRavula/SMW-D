<?php

use kartik\select2\Select2;
use common\models\User;
use common\models\Location;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-form">
<?php $form = ActiveForm::begin([
    'id' => 'modal-form',
    'enableClientValidation' => false,
    'enableAjaxValidation' => true,
    'validationUrl' => Url::to(['extra-lesson/validate-group', 'courseId' => $course->id]),
    'action' => Url::to(['extra-lesson/create-group', 'courseId' => $course->id]),
]); ?>
<?php $this->render('/lesson/_color-code'); ?>
<div class="row">
        
        <?php $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $teachers = ArrayHelper::map(
                    User::find()
                        ->notDeleted()
                        ->teachers($model->programId, $locationId)
                        ->all(),
            'id',
            'publicIdentity'
        );
        ?>
    <div class="col-md-3">
        <?php
        // Dependent Dropdown
        echo $form->field($model, 'teacherId')->widget(Select2::classname(), [
                'data' => $teachers,
                'options' => [
                    'id' => 'teacher-change',
                    'placeholder' => 'Select teacher',
                ]
            ]);
        ?>
    </div>
    <div class="col-md-2">
            <?php
            echo $form->field($model, 'duration')->widget(
            TimePicker::classname(),
                [
                'pluginOptions' => [
                    'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
                    'showMeridian' => false,
                ],
            ]
        );
            ?>
    </div>
    <div class="col-md-3">
            <?php echo $form->field($model, 'date')->textInput([
                'readOnly' => true, 
                'id' => 'extra-gruop-lesson-date'
            ])?>
    </div>
    <div class="col-md-2">
            <?php echo $form->field($model, 'programRate')->textInput()?>
    </div>
    <div class="col-md-2 m-b-20">
            <?php echo $form->field($model, 'applyFullDiscount')->checkbox()?>
    </div>
        
    <div class="col-md-12">
        <div id="lesson-calendar"></div>
    </div>
</div>
<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function() {
        var options = {
            'renderId' : '#lesson-calendar',
            'eventUrl' : '<?= Url::to(['teacher-availability/show-lesson-event']) ?>',
            'availabilityUrl' : '<?= Url::to(['teacher-availability/availability']) ?>',
            'changeId' : '#teacher-change',
            'durationId' : '#extralesson-duration'
        };
        $.fn.calendarDayView(options);
    });

    $(document).on('modal-success', function(event, params) {
        window.location.href = params.url;
        return false;
    });

    $(document).on('week-view-calendar-select', function(event, params) {
        $('#extra-gruop-lesson-date').val(params.date).trigger('change');
        return false;
    });
</script>
