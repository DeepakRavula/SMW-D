<?php

use yii\grid\GridView;
use yii\helpers\Url;
use common\models\vacation;
use common\models\Program;
use yii\helpers\Html;
use common\models\Course;
use yii\bootstrap\Modal;

?>
<?php 
$enrolment = current($model->enrolment);?>
<div class="row p-10">
    <div class="col-md-12">
    <h4 class="pull-left m-r-20">Enrolments</h4>
    <?= Html::a('<i class="fa fa-plus"></i>', ['enrolment', 'id' => $model->id], ['class' => 'add-new-lesson text-add-new']); ?>
    <div class="clearfix"></div>
    </div>
	<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Delete Enrolment Preview</h4>',
        'id' => 'enrolment-preview-modal',
    ]);
    Modal::end();
?>
	<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Add Vacation</h4>',
        'id' => 'vacation-modal',
    ]);?>
	<div class="vacation-content"></div>
   <?php Modal::end();?>
    <div class="grid-row-open">
    <?php yii\widgets\Pjax::begin([
		'id' => 'enrolment-grid',
        'timeout' => 6000,
    ]) ?>
    <?php
    echo GridView::widget([
		'id' => 'enrolment-grid',
        'dataProvider' => $enrolmentDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $url = Url::to(['enrolment/view', 'id' => $model->id]);

                return [
					'data-url' => $url,
					'data-programid' => $model->course->program->id,
					'data-duration' => $model->courseSchedule->duration
				];
            },
        'options' => ['class' => 'col-md-12'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Teacher',
                'value' => function ($data) {
                    return !empty($data->course->teacher->publicIdentity) ? $data->course->teacher->publicIdentity : null;
                },
            ],
            [
                'label' => 'Day',
                'value' => function ($data) {
                    $dayList = Course::getWeekdaysList();
                    $day = $dayList[$data->courseSchedule->day];

                    return !empty($day) ? $day : null;
                },
            ],
            [
                'label' => 'From Time',
                'value' => function ($data) {
                    return !empty($data->courseSchedule->fromTime) ? Yii::$app->formatter->asTime($data->courseSchedule->fromTime) : null;
                },
            ],
            [
                'label' => 'Duration',
                'value' => function ($data) {
                    $duration = \DateTime::createFromFormat('h:i:s', $data->courseSchedule->duration);

                    return !empty($duration) ? $duration->format('H:i') : null;
                },
            ],
            [
                'label' => 'Start Date',
                'value' => function ($data) {
                    return !empty($data->course->startDate) ? Yii::$app->formatter->asDate($data->course->startDate) : null;
                },
            ],
            [
                'label' => 'End Date',
                'value' => function ($data) {
                    return !empty($data->course->endDate) ? Yii::$app->formatter->asDate($data->course->endDate) : null;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{add-vacation}{edit}{delete}',
                'buttons' => [
					'add-vacation' => function ($url, $model) { 
						return Html::a('<i class="fa fa-plane"></i>', '#', [
							'title' => Yii::t('yii', 'Add Vacation'),
							'class' => ['btn-success btn-xs add-new-vacation']
						]);
                    },
					'edit' => function ($url, $model, $key) {
						return Html::a('<i class="fa fa-pencil"></i>','#', [
							'id' => 'enrolment-edit-' . $model->id,
							'title' => Yii::t('yii', 'Edit'),
							'class' => 'enrolment-edit m-l-10 btn-info btn-xs'
						]);
                    },
                    'delete' => function ($url, $model, $key) {
						return Html::a('<i class="fa fa-trash-o"></i>','#', [
							'id' => 'enrolment-delete-' . $model->id,
							'title' => Yii::t('yii', 'Delete'),
							'class' => 'enrolment-delete m-l-10 btn-danger btn-xs'
						]);
                    },
                ],
				'visibleButtons' => [
                    'add-vacation' => function  ($model, $key, $index) {
                        return $model->course->program->isPrivate();
                    },
                ]	
            ],
        ],
    ]);
    ?>
    <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Choose Date, Day and Time</h4>',
	'id' => 'enrolment-edit-modal',
]);
?>
<?php
echo $this->render('_edit-calendar', [
	'course' => $enrolment->course,
	'courseSchedule' => $enrolment->courseSchedule
]);
?>
<?php Modal::end(); ?>