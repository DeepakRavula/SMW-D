<?php
use yii\helpers\Html;
use common\models\Location;
use kartik\select2\Select2;
use common\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use common\models\Lesson;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php $form = ActiveForm::begin([
    'id' => 'modal-form',
    'action' => Url::to(['lesson/update-field', 'id' => $model->id]),
    'enableAjaxValidation' => true,
    'enableClientValidation' => false
]);
?>
<div class="row">
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
    <div class="col-md-2">
        <?= $form->field($model, 'duration')->widget(TimePicker::classname(), [
            'options' => ['id' => 'course-duration'],
            'pluginOptions' => [
                'showMeridian' => false,
            ]
        ]); ?>
    </div>
    <div class="col-md-3 form-group m-t-25 pull-right">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default modal-cancel']); ?>
        <?= Html::submitButton(Yii::t('backend', 'Apply'), [
            'class' => 'btn btn-info modal-save',
            'name' => 'button',
            'value' => Lesson::APPLY_SINGLE_LESSON
        ]) ?>
        <?= Html::submitButton(Yii::t('backend', 'Apply All'), [
            'class' => 'btn btn-info modal-save-all',
            'name' => 'button',
            'value' => Lesson::APPLY_ALL_FUTURE_LESSONS
        ]) ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="lesson-review-edit-calendar"></div>
    </div>
</div>
<?= $form->field($model, 'id')->hiddenInput()->label(false);?>
<?= $form->field($model, 'teacherId')->hiddenInput()->label(false);?>
<?= $form->field($model, 'applyContext')->hiddenInput()->label(false);?>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $('#popup-modal').on('shown.bs.modal', function () {
        var options = {
            'renderId' : '#lesson-review-edit-calendar',
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
</script>