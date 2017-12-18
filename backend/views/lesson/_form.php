<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use kartik\color\ColorInput;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Classroom;
use common\models\User;
use common\models\LocationAvailability;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-qualify">
<?php $form = ActiveForm::begin([
            'id' => 'lesson-edit-form',
            'enableAjaxValidation' => true,
			'enableClientValidation' => false,
            'validationUrl' => Url::to(['lesson/validate-on-update', 'id' => $model->id, 'teacherId' => null]),
            'action' => Url::to(['lesson/update', 'id' => $model->id]),
            'options' => [
                'class' => 'p-10',
            ]
        ]); ?>
    <div class="row">
        <div class="col-md-4">
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
                        ->teachers($model->course->program->id, Yii::$app->session->get('location_id'))
                ->join('LEFT JOIN', 'user_profile','user_profile.user_id = ul.user_id')
                        ->notDeleted()
                        ->orderBy(['user_profile.firstname' => SORT_ASC])
				->all(),
			'id', 'userProfile.fullName'
            ))->label();
            ?>  
        </div>
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
    </div>
    <div class="row">
        <div class="col-md-12">
            <div id="error" style="display:none;" class="alert-danger alert fade in"></div>
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
    <div class="clearfix"></div>
            <?php $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id; ?>
		<?php if($model->course->program->isPrivate() && $model->isUnscheduled()) : ?>
        <div class="row">
            <div class="col-md-4">
                <?php
                if ($privateLessonModel->isNewRecord) {
                    $date = new \DateTime($model->date);
                    $date->modify('90 days');
                    $privateLessonModel->expiryDate = $date->format('d-m-Y');
                }
                ?>
			<?= $form->field($privateLessonModel, 'expiryDate')->widget(DatePicker::classname(), [
                    'options' => [
                        'value' => Yii::$app->formatter->asDate($privateLessonModel->expiryDate),
                    ],
                    'layout' => '{input}{picker}',
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy',
                    ],
                ]);
                ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <?= Html::a('Cancel', '#', ['class' => 'btn btn-default lesson-schedule-cancel']);?> 
            <?= Html::submitButton(Yii::t('backend', 'Save'), ['id' => 'lesson-edit-save', 'class' => 'btn btn-info', 'name' => 'button']) ?>
        <div class="clearfix"></div>
    </div>
    </div>
    </div>
        <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
$(document).ready(function() {
$(document).on('click', '.glyphicon-remove', function () {
        $('#lesson-date').val('').trigger('change');
    });
    });
</script>
