<?php

use kartik\select2\Select2;
use common\models\User;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use common\models\Program;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-form">
<?php $form = ActiveForm::begin([
	'id' => 'lesson-form',
	'enableClientValidation' => false,
	'enableAjaxValidation' => true,
	'validationUrl' => Url::to(['lesson/validate', 'studentId' => $studentModel->id]),
	'action' => Url::to(['lesson/create', 'studentId' => $studentModel->id]),
]); ?>
<div class="row">
        <div class="col-md-6 lesson-program">
            <?php $query = Program::find()
                            ->active()
                            ->privateProgram();

            $allPrograms = $query->all();
            $enrolledPrograms = ArrayHelper::map(
                            $query->studentEnrolled($studentModel->id)
                            ->all(), 'id', 'name');
            

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
    	<div class="col-md-6 lesson-teacher">
        <?php $locationId = Yii::$app->session->get('location_id');
        $teachers = ArrayHelper::map(
                    User::find()
                        ->notDeleted()
                        ->teachers($model->programId, $locationId)
                        ->all(), 'id', 'publicIdentity');
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
        <div class="col-md-6 lesson-date">
            <?php echo $form->field($model, 'date')->widget(DatePicker::classname(), [
                'options' => [
                    'id' => 'extra-lesson-date',
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
        <div class="col-md-12">
            <div id="lesson-calendar"></div>
        </div>
        <div class="clearfix"></div>
    <div class="col-md-12 p-l-20 form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
        <?= Html::a('Cancel', '', ['class' => 'btn btn-default extra-lesson-cancel-button']); ?>
    </div>
    </div>
    <?php echo $form->field($model, 'duration')->hiddenInput()->label(false) ?>
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
</script>
