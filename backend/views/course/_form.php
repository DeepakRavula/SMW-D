<?php

use common\models\Program;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use common\models\LocationAvailability;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\time\TimePicker;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
/* @var $form yii\bootstrap\ActiveForm */

?>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<div id="error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="group-course-form p-10">
	<?php
	$form = ActiveForm::begin([
		'id' => 'group-course-form',
		'enableAjaxValidation' => true,
	]);
	?>
	<div class="row p-10">
		<div class="col-md-3">
			<?php
			echo $form->field($model, 'programId')->widget(Select2::classname(), [
				'data' =>ArrayHelper::map(Program::find()->group()->active()
						->all(), 'id', 'name'),
                            'options' => [
                                    'id' => 'course-programid'
				],
				'pluginOptions' => [
                                    'multiple' => false,
                                    'placeholder' => 'Select Program',
				],
			]);
			?>
		</div>
		<div class="col-md-3">
			<?php
			// Dependent Dropdown
			echo $form->field($model, 'teacherId')->widget(DepDrop::classname(), [
				'options' => ['id' => 'course-teacherid'],
                                 'type' => DepDrop::TYPE_SELECT2,
				'pluginOptions' => [
					'depends' => ['course-programid'],
					'placeholder' => 'Select...',
					'url' => Url::to(['course/teachers']),
				],
			])->label('Teacher');
			?>
		</div>
		<div class="col-md-3">
			<?=
			$form->field($model, 'duration')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'showMeridian' => false,
					'defaultTime' => (new \DateTime('00:30'))->format('H:i'),
				],
				'options' => [
					'class' => 'duration'
				]
			]);
			?>
		</div>
		<div class="col-md-3">
			<?= $form->field($model, 'weeksCount')->textInput()->label('Number Of Weeks'); ?>
		</div>
		<div class="clearfix"></div>
		<div class="padding-v-md">
        <div class="line line-dashed"></div>
    </div>
        <div class="col-md-12">
		<?= $this->render('_form-add-lesson', [
			'form' => $form,
			'courseSchedule' => $courseSchedule
		]); ?>
        </div>
        <div class="col-md-12">
			<div class="pull-right">
				<?php
				if (!$model->isNewRecord) {
					echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-default']);
				}
				?>
                <?php echo Html::submitButton(Yii::t('backend', 'Preview Lessons'), ['id' => 'group-course-save', 'class' => 'btn btn-info', 'name' => 'signup-button'])
				?>
			</div>
		</div>
		<?php ActiveForm::end(); ?>
	</div>

<?php
    $locationId = Yii::$app->session->get('location_id');
    $minLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    $minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>

<script>
    $(document).on('click', '.course-calendar-icon', function() {
        var name = $(this).parent();
        var teacherId = $('#course-teacherid').val();
        var duration = $('#course-duration').val();
        var params = $.param({ id: teacherId });
        if (!$.isEmptyObject(teacherId) && !$.isEmptyObject(duration)) {
            $.ajax({
                url: '<?= Url::to(['teacher-availability/availability-with-events']); ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    var options = {
                        name: name,
                        date: moment(new Date()),
                        duration: duration,
                        businessHours: response.availableHours,
                        minTime: '<?= $minTime; ?>',
                        maxTime: '<?= $maxTime; ?>',
                        eventUrl: '<?= Url::to(['teacher-availability/show-lesson-event']); ?>?lessonId=&teacherId=' + teacherId
                    };
                    $('#calendar-date-time-picker').calendarPicker(options);
                }
            });
            return false;
        }
    });

    $(document).on('change', '#course-teacherid', function() {
        $('.remove-item').click();
        $('.day').val('');
        $('.time').val('');
        return false;
    });

    $(document).on('after-date-set', function(event, params) {
        if (!$.isEmptyObject(params.date)) {
            params.name.find('.lesson-time').find('.time').val(moment(params.date).format('DD-MM-YYYY h:mm A'));
            params.name.find('.lesson-day').find('.day').val(moment(params.date).format('dddd'));
        }
    });
</script>
