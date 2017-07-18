<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use kartik\color\ColorInput;
use yii\helpers\Url;
use wbraganca\selectivity\SelectivityWidget;
use yii\helpers\ArrayHelper;
use common\models\Classroom;
use common\models\User;
use common\models\LocationAvailability;
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';

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
            'enableAjaxValidation' => true,
            'action' => Url::to(['lesson/validate-on-update', 'id' => $model->id]),
            'options' => [
                'class' => 'p-10',
            ]
        ]); ?>
   <div class="row-fluid">
	   <div class="col-md-3">
		    <?php if($model->isUnscheduled()) : ?>
				<?php $model->duration = $model->getCreditUsage(); ?> 
		    <?php endif; ?>
            <?php
            echo $form->field($model, 'duration')->widget(TimePicker::classname(),
                [
                'options' => ['id' => 'course-duration'],
                'pluginOptions' => [
                    'showMeridian' => false,
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
				->joinWith(['qualification' => function($query) use($model){
					$query->andWhere(['program_id' => $model->course->program->id]);
				}])
				->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
				->where(['raa.item_name' => 'teacher'])
				->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
                                ->notDeleted()
				->all(),
			'id', 'userProfile.fullName'
		))->label();
            ?>  
        </div>
	   
	   <div class="col-md-5">
		   <div class="row">
			<div class="col-md-9" style="width:72%;">
				<?php
				echo $form->field($model, 'date')->widget(DateTimePicker::classname(), [
					'options' => [
                                            'id' => 'calendar-date-time-picker',
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
			<div class="col-md-3" style="padding:0;">
					<div class="hand lesson-edit-calendar">
					<p> <label> Calendar View </label></p>
					<span class="fa fa-calendar" style="font-size:30px; margin:-12px 32px;"></span>
				</div>
			</div>
		   </div>
        </div>
	</div>
	<div class="row-fluid">
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
        <div class="form-group col-md-4">
        <?php echo $form->field($model, 'colorCode')->widget(ColorInput::classname(), [
                'options' => [
                    'placeholder' => 'Select color ...',
                    'value' => $model->getColorCode(),
                ],
        ]);
        ?>
        </div>
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

<?php
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


<script type="text/javascript">
$(document).ready(function () {
    $(document).on('click', '.lesson-edit-calendar', function () {
        var teacherId = $('#lesson-teacherid').val();
        var duration = $('#course-duration').val();
        var params = $.param({ id: teacherId });
        $.ajax({
            url: '<?= Url::to(['teacher-availability/availability-with-events']); ?>?' + params,
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                var options = {
                    duration: duration,
                    businessHours: response.availableHours,
                    minTime: '<?= $minTime; ?>',
                    maxTime: '<?= $maxTime; ?>',
                    eventUrl: '<?= Url::to(['calendar/show-events', 'lessonId' => $model->id]); ?>&teacherId=' + teacherId,
                };
                $('#calendar-date-time-picker').calendarPicker(options);
            }
        });
        return false;
        
    });
    
    $(document).on('change', '#calendar-date-time-picker-date', function () {
        $.ajax({
            url: '<?= Url::to(['lesson/validate-on-update', 'id' => $model->id]); ?>',
            type: 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function (response)
            {
                if (!response.status) {
                    $('#calendar-date-time-picker').fullCalendar('removeEvents', 'newEnrolment');
                    $('#calendar-date-time-picker-date').val('');
                    $('#calendar-date-time-picker-error-notification').html(response.error).fadeIn().delay(5000).fadeOut();
                }
            }
        });
        return false;
    });
});
</script>
