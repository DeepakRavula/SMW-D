<?php

use common\models\Lesson;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use common\models\Program;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-form">
<?php if (Yii::$app->controller->id === 'lesson'): ?>
	<?=
        $this->render('view', [
            'model' => $model,
        ]);
    ?>
<?php endif; ?>
<?php $form = ActiveForm::begin([
	'id' => 'lesson-form',
]); ?>
<div class="row p-20">
	<?php if ($model->isNewRecord): ?>
        <div class="col-md-6">
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
    	<div class="col-md-6">
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
		<?php endif; ?>
		<div class="col-md-6">
            <?php 
              if ($model->isNewRecord) {
                  $model->date = (new \DateTime())->format('d-m-Y g:i A');
              }
            ?>
            <?php
                echo $form->field($model, 'date')->widget(DateTimePicker::classname(), [
                       'options' => [
                    'value' => Yii::$app->formatter->asDateTime($model->date),
               ],
                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy HH:ii P',
                    'showMeridian' => true,
                    'minuteStep' => 15,
                ],
              ]);
            ?>
        </div>
        <div class="col-md-4">			
			<?php echo $form->field($model, 'status')->dropDownList(Lesson::lessonStatuses()) ?>
		</div>
        <div class="col-md-4">
            <?php echo $form->field($model, 'notes')->textarea() ?>
        </div>
        <div class="clearfix"></div>
    <div class="col-md-12 p-l-20 form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php if(! $model->isNewRecord) : ?>
            <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']); ?>
		<?php endif; ?>
    </div>
    </div>
<?php ActiveForm::end(); ?>

</div>
