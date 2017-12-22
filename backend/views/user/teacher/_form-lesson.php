<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify">
<?php $form = ActiveForm::begin([
            'id' => 'lesson-edit-form',
            'enableAjaxValidation' => true,
			'enableClientValidation' => false,
            'validationUrl' => Url::to(['lesson/validate-on-update', 'id' => $model->id, 'teacherId' => $model->teacherId]),
            'action' => Url::to(['lesson/substitute', 'id' => $model->id]),
            'options' => [
                'class' => 'p-10',
            ]
        ]); ?>
		<div class="row">
			<?php
            echo $form->field($model, 'duration')->hiddenInput(['value' => (new \DateTime($model->duration))->format('H:i')])->label(false);?>
	   <div class="col-md-4">
        <?php
        // Dependent Dropdown
        echo $form->field($model, 'teacherId')->dropDownList(
            ArrayHelper::map(User::find()
				->teachers($model->course->program->id, \Yii::$app->session->get('location_id'))
                ->join('LEFT JOIN', 'user_profile','user_profile.user_id = ul.user_id')
                ->notDeleted()
                ->orderBy(['user_profile.firstname' => SORT_ASC])
				->all(),
			'id', 'userProfile.fullName'
		))->label('Teacher');
            ?>  
        </div>
        <div class="col-md-5">
		<div class="form-group field-calendar-date-time-picker-date">
                <label class="control-label" for="calendar-date-time-picker-date">Reschedule Date</label>
                <div id="calendar-date-time-picker-date-datetime" class="input-group date">
                    <input type="text" id="lesson-date1" class="form-control" name="Lesson[date]"
                        value='<?php echo $model->date; ?>' readonly>
                    <span class="input-group-addon" title="Clear field">
                        <span class="glyphicon glyphicon-remove"></span>
                    </span>
                </div>
            </div>    
        </div>
        </div>
        <div class="col-md-12">
			<div id="teacher-lesson"></div>
        </div>
	   <div class="clearfix"></div>
		<?php $locationId = \Yii::$app->session->get('location_id'); ?>
   <div class="row">
       <div class="col-md-12">
           <div class="pull-right">
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default lesson-cancel']);?>
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['id' => 'lesson-edit-save', 'class' => 'btn btn-info', 'name' => 'button']); ?>	
		<div class="clearfix"></div>
	</div>
           </div>
   </div>
	<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).on('submit', '#lesson-edit-form', function () {
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
                    $('#lesson-modal').modal('hide');
                    var dateRange = $('#lessonsearch-daterange').val();
                    $.pjax.reload({container: "#teacher-lesson-grid", replace: false, timeout: 6000, data: $('#teacher-lesson-search-form').serialize()});
                    var params = $.param({ 'LessonSearch[dateRange]': dateRange});
                    var url = '<?= Url::to(['print/teacher-lessons', 'id' => $userModel->id]); ?>&' + params;
                    $('#print-btn').attr('href', url);
                } else {
                    $('#error-notification').html(response.errors[0]).fadeIn().delay(5000).fadeOut();
                }
            }
        });
        return false;
    });
    
    $(document).on('click', '.glyphicon-remove', function () {
        $('#lesson-date1').val('').trigger('change');
    });
</script>