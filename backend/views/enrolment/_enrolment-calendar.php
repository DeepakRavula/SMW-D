<?php 
use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use common\models\Holiday;
use common\models\Location;
use kartik\select2\Select2;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\Program;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
?>
<div class="clearfix"></div>

<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<script type="text/javascript" src="/plugins/poshytip/jquery.poshytip.min.js"></script>
<script type="text/javascript" src="/plugins/poshytip/jquery.poshytip.js"></script>
<link type="text/css" href="/plugins/poshytip/tip-yellowsimple/tip-yellowsimple.css" rel='stylesheet' />
<?= $this->render('/lesson/_color-code');?>
<style type="text/css">
   .fc-resource-cell{width:150px;}
   .fc-view.fc-agendaDay-view{overflow-x:scroll;}
</style>
<?php  $locationId = Location::findOne(['slug' => Yii::$app->location])->id; ?>
<?php $form = ActiveForm::begin([
    'id' => 'schedule-form'
]); ?>
	<div class="col-md-2 schedule-picker">
        <?= $form->field($searchModel, 'goToDate', [
            'inputTemplate' => '<div class="input-group m-r-45">{input}</div>',
            ])->widget(DatePicker::classname(), [
                'options' => [
                    'class' => 'form-control',
                    'id' => 'schedule-go-to-datepicker',
                    'readOnly' => true
                ],
                'dateFormat' => 'php:M d, Y',
                'clientOptions' => [
                    'defaultDate' => Yii::$app->formatter->asDate(new \DateTime()),
                    'changeMonth' => true,
                    'yearRange' => '-20:+100',
                    'changeYear' => true,
                ]
            ])->label(false);
        ?>
    </div>
<?php ActiveForm::end(); ?>
        <div class="pull-right calendar-filter">
            <div class="row" style="width:600px">
                <div class="col-md-2">
                    <span class="filter_by_calendar">Filter by</span>
                </div>
                <div class="col-md-5">
                    <?=
                    Select2::widget([
                        'name' => 'program',
                        'data' => ArrayHelper::map(Program::find()
                            ->notDeleted()
                            ->active()
                            ->orderBy(['name' => SORT_ASC])
                            ->all(), 'id', 'name'),
                        'options' => [
                            'placeholder' => 'Program',
                            'id' => 'program-selector'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-5">
                    <?=
                    Select2::widget([
                        'name' => 'teacher',
                        'data' => ArrayHelper::map(User::find()
                            ->notDeleted()
                            ->active()
                            ->teachersInLocation($locationId)
                            ->all(), 'id', 'publicIdentity'),
                        'options' => [
                            'placeholder' => 'Teacher',
                            'id' => 'teacher-selector'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
            </div>
       </div>
<div id='calendar'></div>