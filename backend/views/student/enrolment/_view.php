<?php

use yii\grid\GridView;
use yii\helpers\Url;
use common\models\vacation;
use common\models\Program;
use yii\helpers\Html;
use common\models\Course;
use yii\bootstrap\Modal;

?>
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

                return ['data-url' => $url];
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
                'template' => '{add-vacation}{delete}',
                'buttons' => [
					'add-vacation' => function ($url, $model) { 
                        //$url = Url::to(['vacation/create', 'enrolmentId' => $model->id]);
						return Html::a('Add Vacation', '#', [
							'title' => Yii::t('yii', 'Add Vacation'),
							'class' => ['btn-success btn-sm add-new-vacation']
						]);
                    },
                    'delete' => function ($url, $model, $key) {
						return Html::a('Delete','#', [
							'id' => 'enrolment-delete-' . $model->id,
							'class' => 'enrolment-delete m-l-10 btn-danger btn-sm'
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