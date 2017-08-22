<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\date\DatePicker;
use common\models\Lesson;
use yii\widgets\ActiveForm;

?>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<script type="text/javascript" src="/admin/plugins/fullcalendar-time-picker/fullcalendar-time-picker.js"></script>
<?php
    Modal::begin([
            'header' => '<h4 class="m-0">Choose Date, Day and Time</h4>',
            'id' => 'calendar-date-time-picker-modal',
    ]);
?>
<div id="calendar-date-time-picker-error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="row-fluid">
    <div class="form-group">
        <?php $lessonModel = new Lesson();
        $form = ActiveForm::begin([
           'id' => 'lesson-form'
        ]); ?>
            <?= $form->field($lessonModel, 'date')->hiddenInput([
                'id' => 'calendar-date-time-picker-date',
            ])->label(false);?>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-lg-2 pull-right">
        <?php echo '<label>Go to Date</label>'; ?>
        <?php echo DatePicker::widget([
                'name' => 'selected-date',
                'id' => 'go-to-date',
                'value' => Yii::$app->formatter->asDate((new \DateTime())->format('d-m-Y')),
                'type' => DatePicker::TYPE_INPUT,
                'buttonOptions' => [
                    'removeButton' => true,
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                    'todayHighlight' => true
                ]
        ]); ?>
    </div>
    <div id="calendar-date-time-picker" ></div>
</div>
 <div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary calendar-date-time-picker-save', 'name' => 'button']) ?>
	<?= Html::a('Cancel', '#', ['class' => 'btn btn-default calendar-date-time-picker-cancel']);
	?>
	<div class="clearfix"></div>
</div>
<?php Modal::end(); ?>



