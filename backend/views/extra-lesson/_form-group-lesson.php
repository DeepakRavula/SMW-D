<?php

use common\components\select2\Select2;
use common\models\User;
use common\models\Location;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
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
            <div class="col-lg-2 pull-right">
            <?php echo '<label>Go to Date</label>'; ?>
            <?php echo DatePicker::widget([
                    'name' => 'selected-date',
                    'id' => 'extra-group-lesson-go-to-date',
                    'value' => Yii::$app->formatter->asDate((new DateTime())->format('d-m-Y')),
                    'type' => DatePicker::TYPE_INPUT,
                    'buttonOptions' => [
                        'removeButton' => true,
                    ],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy',
                        'todayHighlight' => true
                    ]
            ]); ?>
        </div>
            <div id="lesson-calendar"></div>
        </div>
</div>
<?php ActiveForm::end(); ?>
</div>
<?php
    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
    $minLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    $minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>

<script>
$(document).ready(function() {
    var options = {
        'dateId' : '#extra-group-lesson-go-to-date',
        'renderId' : '#lesson-calendar',
        'eventUrl' : '<?= Url::to(['teacher-availability/show-lesson-event']) ?>',
        'availabilityUrl' : '<?= Url::to(['teacher-availability/availability-with-events']) ?>',
        'dateRenderId' : '#extra-gruop-lesson-date',
        'changeId' : '#teacher-change',
        'durationId' : '#extralesson-duration',
        'minTime': '<?= $minTime; ?>',
        'maxTime': '<?= $maxTime; ?>'
    };
    $.fn.calendarDayView(options);
}); 
$(document).on('modal-success', function(event, params) {
    window.location.href = params.url;
    return false;
});
</script>
