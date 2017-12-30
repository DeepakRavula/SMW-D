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
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<div id="bulk-reschedule" style="display: none;" class="alert-danger alert fade in"></div>
<div class="enrolment-form">
	<?php $form = ActiveForm::begin([
		'id' => 'enrolment-update'
	]); ?>
    <div class="row">
        <div class="col-md-3">
			<?php $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
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
                    'id' => 'course-teacherid',
                    'placeholder' => 'Select teacher',
                ],
                'pluginOptions' => [
                    'depends' => ['course-program'],
                    'url' => Url::to(['/course/teachers'])
                ]
            ]);
        ?>
		</div>
		<div class="col-md-2">
	<?= $form->field($courseSchedule, 'day', ['horizontalCssClasses' => [
		'label' => '',
		'wrapper' => '',
]])->textInput(['readOnly' => true])->label('Day');?>
		</div>
		<div class="col-md-4">
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
                    <div id="enrolment-calendar">
                        <?= $this->render('_calendar', [
							'model' => $model,
						]);?>
                    </div>
                    <div class="pull-right m-t-10">
		<?= Html::a('Cancel', '', ['class' => 'btn btn-default enrolment-edit-cancel']); ?>
        <?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'),
	['class' => 'btn btn-info', 'name' => 'signup-button', 'id' => 'preview-button']) ?>

    </div>
        </div>
	</div>

<?php ActiveForm::end(); ?>

</div>