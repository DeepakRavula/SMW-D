<?php

use common\models\Lesson;
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
        <div id="lesson-program" class="col-md-6">
            <?php echo $form->field($model, 'programId')->dropDownList(
                    ArrayHelper::map(
                        Program::find()
                            ->joinWith(['course' => function ($query) use ($studentModel) {
                                $query->joinWith(['enrolment' => function ($query) use ($studentModel) {
                                    $query->where(['studentId' => $studentModel->id]);
                                }]);
                            }])
                        ->all(),
                     'id', 'name'), ['prompt' => 'Select Program'])->label()
            ?>  
        </div>
    	<div id="lesson-teacher" class="col-md-6">
        <?php
        // Dependent Dropdown
        echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
            'options' => ['id' => 'lesson-teacherid'],
            'pluginOptions' => [
                'depends' => ['lesson-programid'],
                'placeholder' => 'Select...',
                'url' => Url::to(['/course/teachers']),
            ],
        ]);
        ?>
        </div>
        <div id="lesson-date" class="col-md-6">
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
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        <?= Html::a('Cancel', '', ['class' => 'btn extra-lesson-cancel-button']); ?>
    </div>
    </div>
    <?php echo $form->field($model, 'duration')->hiddenInput()->label(false) ?>
<?php ActiveForm::end(); ?>

</div>
