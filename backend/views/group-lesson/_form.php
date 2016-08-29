<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use common\models\Lesson;
/* @var $this yii\web\View */
/* @var $model common\models\GroupLesson */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="group-lesson-form">
<?=
	$this->render('view', [
    	'model' => $model,
    ]);
?>
<?php $form = ActiveForm::begin(); ?>

   	<div class="row p-20">
    <div class="row-fluid">
		<div class="col-md-4">
            <?php
            echo $form->field($model, 'date')->widget(DateTimePicker::classname(), [
               'options' => [
                    'value' => Yii::$app->formatter->asDateTime($model->date),
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
            <?php echo $form->field($model, 'status')->dropDownList(
                   Lesson::lessonStatuses())
            ?>  
        </div>
    </div>
        <div class="col-md-8">
            <?php echo $form->field($model, 'notes')->textarea() ?>
        </div> 
    </div>
    <div class="row-fluid">
    <div class="p-l-20 form-group">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        <?php
        if (!$model->isNewRecord) {
            echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
        }
        ?>
    </div>
    </div>

<?php ActiveForm::end(); ?>

</div>
