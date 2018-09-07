<?php

use kartik\select2\Select2;
use common\models\User;
use common\models\Location;
use yii\bootstrap\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use common\models\Program;
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
    'validationUrl' => Url::to(['extra-lesson/validate-private', 'studentId' => $studentModel->id]),
    'action' => Url::to(['extra-lesson/create-private', 'studentId' => $studentModel->id]),
]); ?>
<?php $this->render('/lesson/_color-code'); ?>
<div class="row">
        <div class="col-md-3 lesson-program">
            <?php $query = Program::find()
                            ->notDeleted()
                            ->active()
                            ->privateProgram();

            $allPrograms = $query->all();
            $enrolledPrograms = ArrayHelper::map(
                            $query->studentEnrolled($studentModel->id)
                            ->all(),
                'id',
                'name'
            );
            $programs = [];
            foreach ($allPrograms as $program) {
                $programs[] = [
                    'id' => $program->id,
                    'text' => $program->name
                ];
            }
            $allProgram = yii\helpers\Json::encode($programs);
            ?>
            <?php echo $form->field($model, 'programId')->widget(Select2::classname(), [
                'data' => $enrolledPrograms,
                'options' => ['placeholder' => 'Select program', 'id' => 'lesson-program']
            ])->label('Program - <a id="show-all">Click to show all</a>'); ?>
        </div>
    	<div class="col-md-4 lesson-teacher">
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
        <?php
        // Dependent Dropdown
        echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
                'data' => $teachers,
                'type' => DepDrop::TYPE_SELECT2,
                'options' => [
                    'id' => 'lesson-teacher',
                    'placeholder' => 'Select teacher',
                ],
                'pluginOptions' => [
                    'depends' => ['lesson-program'],
                    'url' => Url::to(['/course/teachers'])
                ]
            ]);
        ?>
        </div>
        <div class="col-md-2 lesson-duration">
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
        <div class="col-md-3 lesson-date">
            <?php echo $form->field($model, 'date')->textInput([
               'readOnly' => true,
                'id' => 'extra-lesson-date',
            ])?>
        </div>
        
        <div class="col-md-12">
            <div id="extra-lesson-calendar"></div>
        </div>
</div>
<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function () {
        $(document).on('click', '#show-all', function () {
            var data = <?php echo $allProgram; ?>;
            $("#lesson-program").select2({
                data: data,
                width: '100%',
                theme: 'krajee'
            });
            return false;
        });
    });

    $('#popup-modal').on('shown.bs.modal', function () {
        var options = {
            'renderId' : '#extra-lesson-calendar',
            'eventUrl' : '<?= Url::to(['teacher-availability/show-lesson-event']) ?>',
            'availabilityUrl' : '<?= Url::to(['teacher-availability/availability']) ?>',
            'changeId' : '#lesson-teacher',
            'durationId' : '#extralesson-duration',
            'studentId' : '<?= $studentModel->id ?>'
        };
        $.fn.calendarDayView(options);
    });

    $(document).on('week-view-calendar-select', function(event, params) {
        $('#extra-lesson-date').val(moment(params.date, "DD-MM-YYYY h:mm a").format('MMM D, Y hh:mm A')).trigger('change');
        return false;
    });
</script>
