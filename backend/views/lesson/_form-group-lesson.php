<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use common\models\User;
use yii\helpers\Url;
use wbraganca\selectivity\SelectivityWidget;
use common\models\Classroom;
use kartik\color\ColorInput;

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
                    'value' => $model->isUnscheduled() ? '' : Yii::$app->formatter->asDateTime($model->date),
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
        <?php
        // Dependent Dropdown
        echo $form->field($model, 'teacherId')->dropDownList(
            ArrayHelper::map(User::find()
				->joinWith(['userLocation ul' => function ($query) {
					$query->joinWith('teacherAvailability');
				}])
				->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
				->where(['raa.item_name' => 'teacher'])
				->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
				->all(),
			'id', 'userProfile.fullName'
		))->label();
            ?>  
        </div>
	<div class=" col-md-4">
		   <?php $locationId = Yii::$app->session->get('location_id'); ?>
		   <?=
                $form->field($model, 'classroomId')->widget(SelectivityWidget::classname(), [
                    'pluginOptions' => [
                        'allowClear' => true,
                        'items' => ArrayHelper::map(Classroom::find()->andWhere(['locationId' => $locationId])->all(), 'id', 'name'),
                        'placeholder' => 'Select Classroom',
                    ],
                ]);
                ?>
		</div>
        <div class="form-group col-md-3">
        <?php echo $form->field($model, 'colorCode')->widget(ColorInput::classname(), [
                'options' => [
                    'placeholder' => 'Select color ...',
                    'value' => $model->getColorCode(),
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