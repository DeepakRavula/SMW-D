<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use common\models\User;
use yii\helpers\Url;
use common\models\Course;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div id="bulk-reschedule" style="display: none;" class="alert-danger alert fade in"></div>
<div class="enrolment-form">
	<?php $form			 = ActiveForm::begin([
		'id' => 'enrolment-update'
	]); ?>
    <div class="row">
        <div class="col-md-3">
			<?php $locationId = Yii::$app->session->get('location_id');
			$teachers = ArrayHelper::map(
				User::find()
					->notDeleted()
					->teachers($course->programId, $locationId)
                    ->join('LEFT JOIN', 'user_profile','user_profile.user_id=ul.user_id')
                    ->orderBy(['user_profile.firstname'=> SORT_ASC])
					->all(), 'id', 'publicIdentity');
			?>
			<?php
        // Dependent Dropdown
        echo $form->field($course, 'teacherId')->widget(DepDrop::classname(), [
                'data' => $teachers,
                'type' => DepDrop::TYPE_SELECT2,
                'options' => [
                    'id' => 'course-teacher',
                    'placeholder' => 'Select teacher',
                ],
                'pluginOptions' => [
                    'depends' => ['course-program'],
                    'url' => Url::to(['/course/teachers'])
                ]
            ]);
        ?>
		</div>
		<div class="col-md-3">
			<?php echo $form->field($courseSchedule, 'day')->dropdownList(Course::getWeekdaysList(), ['prompt' => 'select day']) ?>
		</div>
		<div class="col-md-3">
			<?php
			echo $form->field($course, 'startDate')->widget(DatePicker::classname(),
				[
				'options' => [
					'value' => (new \DateTime())->format('d-m-Y'),
				],
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
				'pluginOptions' => [
					'autoclose' => true,
					'format' => 'dd-mm-yyyy'
				]
			]);
			?>
		</div>
		<div class="col-md-3">
			<?php
			echo $form->field($course, 'endDate')->widget(DatePicker::classname(),
				[
				'options' => [
					'value' => (new \DateTime($course->endDate))->format('d-m-Y'),
				],
				'type' => DatePicker::TYPE_COMPONENT_APPEND,
				'pluginOptions' => [
					'autoclose' => true,
					'format' => 'dd-mm-yyyy'
				]
			]);
			?>
		</div>
        <?= $form->field($courseSchedule, 'fromTime')->hiddenInput()->label(false);?>
        <?= $form->field($courseSchedule, 'duration')->hiddenInput()->label(false);?>
		<div class="col-md-12">
            <div id="enrolment-calendar"></div>
        </div>
	</div>
	<div class="clearfix"></div>
    <div class="form-group">
<?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'),
	['class' => 'btn btn-info', 'name' => 'signup-button', 'id' => 'preview-button']) ?>
		<?= Html::a('Cancel', '', ['class' => 'btn btn-default enrolment-edit-cancel']); ?>
    </div>

<?php ActiveForm::end(); ?>

</div>