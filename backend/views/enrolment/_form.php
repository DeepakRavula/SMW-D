<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use common\models\Course;
use common\models\TeacherAvailability;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?=
$this->render('_view-enrolment', [
    'model' => $model->enrolment,
]);
?>
<div>
<div class="smw-box col-md-10 m-l-10 m-b-10 monthly-estimate">
<?php
    $query = TeacherAvailability::find()
                ->notDeleted()
                ->joinWith('userLocation')
                ->andWhere(['user_id' => $model->teacherId]);
        $teacherDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]); ?>
	<?= $this->render('_teacher-availability', [
        'teacherDataProvider' => $teacherDataProvider,
    ]); ?>
	</div>
	</div>
<div class="enrolment-form form-well form-well-smw">
		<?php $form			 = ActiveForm::begin(); ?>
    <div class="row">
		<?php
        $fromTime		 = Yii::$app->formatter->asTime($courseSchedule->fromTime);
        $courseSchedule->fromTime = !empty($courseSchedule->fromTime) ? $fromTime : null;
        $courseSchedule->paymentFrequency = $model->enrolment->paymentFrequency;
        ?>
        <div class="col-md-4">
<?php echo $form->field($courseSchedule, 'day')->dropdownList(Course::getWeekdaysList(), ['prompt' => 'select day']) ?>
        </div>
		<div class="col-md-4">
<?= $form->field($courseSchedule, 'fromTime')->widget(TimePicker::classname(), []); ?>
		</div>
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'rescheduleBeginDate')->widget(
            DatePicker::classname(),
                [
                'options' => [
                    'value' => (new \DateTime())->format('d-m-Y'),
                ],
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy'
                ]
            ]
        );
            ?>
		</div>
	</div>
    <div class="form-group pull-right">
        <?= Html::a('Cancel', ['view', 'id' => $model->enrolment->id], ['class' => 'btn btn-cancel']);
        ?>
<?php echo Html::submitButton(
            Yii::t('backend', 'Preview Lessons'),
    ['class' => 'btn btn-info', 'name' => 'signup-button']
        ) ?>
		
    </div>

<?php ActiveForm::end(); ?>
<div class="clearfix"></div>

</div>