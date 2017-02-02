<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use kartik\color\ColorInput;
use common\models\Lesson;
use wbraganca\selectivity\SelectivityWidget;
use yii\helpers\ArrayHelper;
use common\models\Classroom;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="lesson-qualify">

	<?=
        $this->render('_view', [
            'model' => $model,
        ]);
    ?>

<?php $form = ActiveForm::begin([
            
            'options' => [
                'class' => 'p-10'
             ]
        ]); ?>
   <div class="row">
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
            ])->label('Reschedule Date');
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
				->joinWith(['qualification' => function($query) use($model){
					$query->andWhere(['program_id' => $model->course->program->id]);
				}])
				->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
				->where(['raa.item_name' => 'teacher'])
				->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
				->all(),
			'id', 'userProfile.fullName'
		))->label();
            ?>  
        </div>
        <div class="col-md-4">
            <?php
            echo $form->field($model, 'duration')->widget(TimePicker::classname(),
                [
                'options' => ['id' => 'course-duration'],
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => Yii::$app->formatter->asDateTime($model->duration),
                ],
            ]);
            ?>
        </div>
		<div class="col-md-4">
			<?php
                if ($privateLessonModel->isNewRecord) {
                    $date = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
                    $date->modify('90 days');
                    $privateLessonModel->expiryDate = $date->format('d-m-Y H:i:s');
                }
            ?>
			<?= $form->field($privateLessonModel, 'expiryDate')->widget(DateTimePicker::classname(), [
                'options' => [
                    'value' => Yii::$app->formatter->asDateTime($privateLessonModel->expiryDate),
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
    <div class="col-md-12 p-l-20 form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'button']) ?>
		<?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
        ?>
		<div class="clearfix"></div>
	</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>
