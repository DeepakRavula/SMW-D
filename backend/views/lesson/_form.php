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
<?php if(Yii::$app->controller->id === 'lesson'): ?>
	<?=
		$this->render('view', [
    		'model' => $model,
    	]);
	?>
<?php endif;?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
	<?php if($model->isNewRecord): ?>
        <div class="col-md-4">
            <?php echo $form->field($model, 'program_id')->dropDownList(
                    ArrayHelper::map(
                        Program::find()
                        ->join('INNER JOIN', 'enrolment','enrolment.program_id = program.id')
                        ->join('INNER JOIN', 'student', 'student.id = enrolment.student_id')
                        ->where(['student.id' =>$studentModel->id])                             
                        ->all(),
                     'id','name'),['prompt'=>'Select Program'] )->label() 
            ?>  
        </div>
    	<div class="col-md-4">
        <?php
        // Dependent Dropdown
        echo $form->field($model, 'teacher_id')->widget(DepDrop::classname(), [
        	'options' => ['id' => 'lesson-teacher_id'],
            'pluginOptions' => [
                'depends' => ['lesson-program_id'],
                'placeholder' => 'Select...',
                'url' => Url::to(['/enrolment/teachers'])
            ]
        ]);
        ?>
        </div>
		<?php endif;?>
		<div class="col-md-4">
            <?php 
              if($model->isNewRecord){
                  $model->date = date('d-m-Y g:i A');
              }
            ?>
            <?php
            echo $form->field($model, 'date')->widget(DateTimePicker::classname(), [
               'options' => [
                    'value' => date("d-m-Y g:i A", strtotime($model->date)),
               ],
                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy HH:ii P'
                ]
            ]);
            ?>
        </div>	
        <div class="col-md-4">
            <?php echo $form->field($model, 'notes')->textarea() ?>
        </div> 
    </div>
    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        <?php
        if (!$model->isNewRecord) {
            echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
        }
        ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
