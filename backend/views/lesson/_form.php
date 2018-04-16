<?php
use common\models\LocationAvailability;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use kartik\time\TimePicker;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Location;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify">
<?php $form = ActiveForm::begin([
            'id' => 'modal-form',
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validationUrl' => Url::to(['lesson/validate-on-update', 'id' => $model->id, 'teacherId' => null]),
            'action' => Url::to(['lesson/update', 'id' => $model->id]),
            'options' => [
                'class' => 'p-10',
            ]
        ]); ?>
    <div class="row">
        <div class="col-md-2">
            <?php
            echo $form->field($model, 'duration')->widget(
            TimePicker::classname(),
                [
                'options' => ['id' => 'course-duration'],
                'pluginOptions' => [
                    'showMeridian' => false,
                ],
            ]
        );
            ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'teacherId')->widget(
                Select2::classname(),
                [
                'data' => ArrayHelper::map(User::find()
                        ->teachers(
                            $model->course->program->id,
                            Location::findOne(['slug' => \Yii::$app->location])->id
                        )
                        ->join(
                            'LEFT JOIN',
                            'user_profile',
                            'user_profile.user_id = ul.user_id'
                        )
                        ->notDeleted()
                        ->orderBy(['user_profile.firstname' => SORT_ASC])
                        ->all(), 'id', 'userProfile.fullName'),
                'options' => [
                    'id' => 'lesson-teacherid'
                ]
                ]
            )->label('Teacher');
            ?>
        </div>
        <div class="col-md-3">
            <?php echo $form->field($model, 'date', [
                'inputTemplate' => '<div class="input-group">{input}<span class="input-group-addon" title="Clear field">
                    <span class="glyphicon glyphicon-remove"></span></span></div>'
                ])->textInput([
                    'id' => 'lesson-date',
                    'readonly' => true,
                    'value' => !$model->isUnscheduled() ? Yii::$app->formatter->asDateTime($model->date) : '',
                ])->label('Reschedule Date');
            ?>
        </div>
        <?php if ($privateLessonModel) : ?>
        <div class="col-md-2">
            <?= $form->field($privateLessonModel, 'expiryDate')->widget(
                DatePicker::classname(), [
                    'value'  => Yii::$app->formatter->asDate($privateLessonModel->expiryDate),
                    'dateFormat' => 'php:M d, Y',
                    'options' => [
                        'class' => 'form-control'
                    ],
                    'clientOptions' => [
                        'changeMonth' => true,
                        'yearRange' => '1500:3000',
                        'changeYear' => true
                    ]
                ]);
            ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="error" style="display:none;" class="alert-danger alert fade in"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="lesson-edit-calendar"></div>
        </div>
    </div>
        <?php ActiveForm::end(); ?>
</div>
<?php
    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
    $minLocationAvailability = LocationAvailability::find()
        ->location($locationId)
        ->locationaAvailabilityHours()
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->location($locationId)
        ->locationaAvailabilityHours()
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    if (empty($minLocationAvailability)) {
        $minTime = LocationAvailability::DEFAULT_FROM_TIME;
    } else {
        $minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    }
    if (empty($maxLocationAvailability)) {
        $maxTime = LocationAvailability::DEFAULT_TO_TIME;
    } else {
        $maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
    }
?>

<script type="text/javascript">
    $('#popup-modal').on('shown.bs.modal', function () {
        var options = {
            'renderId' : '#lesson-edit-calendar',
            'eventUrl' : '<?= Url::to(['teacher-availability/show-lesson-event', 'studentId' => $model->enrolment->studentId]) ?>',
            'availabilityUrl' : '<?= Url::to(['teacher-availability/availability-with-events']) ?>',
            'changeId' : '#lesson-teacherid',
            'durationId' : '#course-duration',
            'lessonId' : '<?= $model->id; ?>'
        };
        $.fn.calendarDayView(options);
    });

    $(document).on('week-view-calendar-select', function(event, params) {
        $('#lesson-date').val(moment(params.date, "DD-MM-YYYY h:mm a").format('MMM D, Y hh:mm A')).trigger('change');
        return false;
    });

    $(document).on('click', '.glyphicon-remove', function () {
        $('#lesson-date').val('').trigger('change');
    });

    $(document).on('modal-success', function(event, params) {
        window.location.href = params.url;
        return false;
    });
</script>
