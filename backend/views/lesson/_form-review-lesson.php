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
<div  id="add-review-lesson-modal" class="invoice-line-item-form">
    <?php $form = ActiveForm::begin(); ?>
 	<div class="row">
        <div class="col-xs-8">
    		<?= $form->field($model, 'date')->widget(DateTimePicker::classname(), [
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
	</div>
    <div class="form-group">
       <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>