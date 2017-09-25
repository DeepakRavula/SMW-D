<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\LocationAvailability;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify">
<?php $form = ActiveForm::begin([
            'id' => 'lesson-edit-form',
            'enableAjaxValidation' => true,
			'enableClientValidation' => false,
            'validationUrl' => Url::to(['lesson/validate-on-update', 'id' => $model->id]),
            'action' => Url::to(['lesson/update', 'id' => $model->id]),
            'options' => [
                'class' => 'p-10',
            ]
        ]); ?>
		<div class="row">
	   <div class="col-md-4">
        <?php
        // Dependent Dropdown
        echo $form->field($model, 'teacherId')->dropDownList(
            ArrayHelper::map(User::find()
				->teachers($model->course->program->id, Yii::$app->session->get('location_id'))
                ->join('LEFT JOIN', 'user_profile','user_profile.user_id = ul.user_id')
                ->notDeleted()
                ->orderBy(['user_profile.firstname' => SORT_ASC])
				->all(),
			'id', 'userProfile.fullName'
		))->label('Teacher');
            ?>  
        </div>
        <div class="col-md-5">
		<?= $form->field($model, 'date')->widget(DatePicker::classname(), [
                'options' => [
                    'value' => Yii::$app->formatter->asDateTime($model->date),
					'readOnly' => true,
                ],
				'layout' => '{input}{remove}',
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
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
	   <div class="clearfix"></div>
		<?php $locationId = Yii::$app->session->get('location_id'); ?>
   <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['id' => 'lesson-edit-save', 'class' => 'btn btn-info', 'name' => 'button']) ?>
		<?= Html::a('Cancel', '#', ['class' => 'btn btn-default lesson-cancel']);
        ?>
		<div class="clearfix"></div>
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
$(document).on('click', '.lesson-edit-calendar', function () {
    $('#lesson-modal').modal('hide');
    var teacherId = $('#lesson-teacherid').val();
    var duration = $('#course-duration').val();
    var params = $.param({teacherId: '' });
    $('#calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
		height:'auto',
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: "<?php echo $minTime; ?>",
        maxTime: "<?php echo $maxTime; ?>",
        slotDuration: "00:15:00",
        droppable: false,
        events: {
            url: '<?= Url::to(['schedule/render-day-events']) ?>?' + params,
            type: 'GET',
            error: function() {
                $("#calendar").fullCalendar("refetchEvents");
            }
        },
        allDaySlot:false,
        editable: true,
    });
    return false;
});
</script>
