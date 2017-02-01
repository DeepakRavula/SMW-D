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
        $this->render('_view', [
            'model' => $model,
        ]);
    ?>
<?php endif; ?>
<?php $form = ActiveForm::begin([
	'id' => 'group-lesson-form',
]); ?>
<div class="row p-10">
	<div class="col-md-4">
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