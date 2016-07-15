<?php

use common\models\Lesson;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-form">

<?php $form = ActiveForm::begin(); ?>

   	<div class="row">
        <div class="col-xs-4">
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
            <?php if (!$model->getIsNewRecord()) : ?>
            <?php echo $form->field($model, 'status')->dropDownList(Lesson::lessonStatuses()); ?> 
            <?php endif; ?>
        </div>         
        <div class="clearfix"></div>
        <div class="col-md-4">
            <?php echo $form->field($model, 'notes')->textarea() ?>
        </div> 
    </div>
    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?php
        if (!$model->isNewRecord) {
            echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
        }
        ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
