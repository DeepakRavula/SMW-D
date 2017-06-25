<?php
use yii\grid\GridView;
use common\models\Course;
use yii\helpers\Url;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Enrolments</h4>
</div>
    <div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
]) ?>
<?php
    echo GridView::widget([
        'dataProvider' => $enrolmentDataProvider,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
		'rowOptions' => function ($model, $key, $index, $grid) {
			$url = Url::to(['enrolment/update', 'id' => $model->id]);

			return ['data-url' => $url];
		},
        'columns' => [
            [
                'label' => 'Student Name',
                'value' => function ($data) {
                    return !empty($data->student->fullName) ? $data->student->fullName : null;
                },
            ],
            [
                'label' => 'Program Name',
                'value' => function ($data) {
                    return !empty($data->program->name) ? $data->program->name : null;
                },
            ],
            [
                'label' => 'Teacher Name',
                'value' => function ($data) {
                    return !empty($data->lessons[0]->teacher->publicIdentity) ? $data->lessons[0]->teacher->publicIdentity : null;
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
                'label' => 'Renewal Date',
                'value' => function ($data) {
                    return !empty($data->course->endDate) ? Yii::$app->formatter->asDate($data->course->endDate) : null;
                },
            ],
        ],
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>
</div>