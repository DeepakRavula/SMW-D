<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;

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

   	<div class="row">
		<?php
		$fromTime = \DateTime::createFromFormat('H:i:s', $model->from_time);
		$model->from_time = ! empty($model->from_time) ? $fromTime->format('g:i A') : null;
		$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s',$model->date);
	?>
		<div class="col-md-4">
		<?= $form->field($model, 'from_time')->widget(TimePicker::classname(), []); ?>
		</div>
		<div class="col-md-4">
            <?php
            echo $form->field($model, 'date')->widget(DateTimePicker::classname(), [
               'options' => [
                    'value' => $lessonDate->format('d-m-Y g:i A'),
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
