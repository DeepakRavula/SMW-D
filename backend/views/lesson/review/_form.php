<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\time\TimePicker;
use common\models\Lesson;
/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>
<?php $form = ActiveForm::begin([
	'id' => 'lesson-review-form',
	'enableAjaxValidation' => true,
	'enableClientValidation' => false
]);
?>
<div class="row">
			   <div class="col-md-9">
                               <div class="col-md-4">
            <div class="form-group field-calendar-date-time-picker-date">
                <label class="control-label" for="calendar-date-time-picker-date">Reschedule Date</label>
                <div id="calendar-date-time-picker-date-datetime" class="input-group date">
                    <input type="text" id="lesson-date" class="form-control" name="Lesson[date]"
                           value='<?php echo Yii::$app->formatter->asDateTime($model->date); ?>' readonly>
                    <span class="input-group-addon" title="Clear field">
                        <span class="glyphicon glyphicon-remove"></span>
                    </span>
                </div>
            </div>       
                               </div>
	<div class="col-md-2">
		<?php
		echo $form->field($model, 'duration')->widget(TimePicker::classname(), [
			'options' => ['id' => 'course-duration'],
			'pluginOptions' => [
				'showMeridian' => false,
			],
		]);
		?>
	</div>
                                </div>
    <div class="col-md-3 form-group m-t-25">
        <div class="pull-right">
		<?= Html::submitButton(Yii::t('backend', 'Apply'), [
			'id' => 'lesson-review-apply',
			'class' => 'btn btn-info',
			'name' => 'button',
			'value' => Lesson::APPLY_SINGLE_LESSON
		]) ?>
		<?= Html::submitButton(Yii::t('backend', 'Apply All'), [
			'id' => 'lesson-review-apply-all',
			'class' => 'btn btn-info',
			'name' => 'button',
			'value' => Lesson::APPLY_ALL_FUTURE_LESSONS
		]) ?>
		<?= Html::a('Cancel','#', ['id' => 'lesson-review-cancel','class' => 'btn btn-default']);
		?>
		<div class="clearfix"></div>
        </div>
	</div>
</div>
     <div class="row">
        <div class="col-md-12">
            <div id="lesson-edit-calendar">
                <div id="loadingspinner" class="spinner" style="" >
                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                    <span class="sr-only">Loading...</span>
                </div>  
            </div>
        </div>
    </div>
    <div id="spinner" class="spinner col-md-4 col-md-offset-4" style="display:none;">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
<span class="sr-only">Loading...</span>
    </div>
	<?= $form->field($model, 'id')->hiddenInput()->label(false);?>
	<?= $form->field($model, 'applyContext')->hiddenInput()->label(false);?>
<div class="row">
	<div class="col-md-12 p-l-20 form-group">
            <div class="pull-right">
            <?= Html::a('Cancel','#', ['id' => 'lesson-review-cancel','class' => 'btn btn-default']);
		?>
		<?= Html::submitButton(Yii::t('backend', 'Apply'), [
                    
			'id' => 'lesson-review-apply',
			'class' => 'btn btn-info',
			'name' => 'button',
			'value' => Lesson::APPLY_SINGLE_LESSON
		]) ?>
		<?= Html::submitButton(Yii::t('backend', 'Apply All'), [
			'id' => 'lesson-review-apply-all',
			'class' => 'btn btn-info',
			'name' => 'button',
			'value' => Lesson::APPLY_ALL_FUTURE_LESSONS
		]) ?>
		<div class="clearfix"></div>
	</div>
        </div>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
$(document).ready(function() {
$(document).on('click', '.glyphicon-remove', function () {
        $('#lesson-date').val('').trigger('change');
    });
    });
</script>