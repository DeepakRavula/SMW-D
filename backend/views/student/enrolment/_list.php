<?php
use yii\grid\GridView;
use common\models\Course;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="grid-row-open"> 
<?php yii\widgets\Pjax::begin([
    'id' => 'student-enrolment-list',
    'timeout' => 6000,
    'enablePushState'=>false,
]) ?>

<?php
echo GridView::widget([
    'id' => 'student-enrolment-grid',
    'dataProvider' => $enrolmentDataProvider,
    'summary' => false,
        'emptyText' => false,
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['enrolment/view', 'id' => $model->id]);

        return [
            'data-url' => $url,
            'data-programid' => $model->course->program->id,
            'data-duration' => $model->courseSchedule->duration
        ];
    },
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-condensed'],
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
                return !empty($data->endDateTime) ? Yii::$app->formatter->asDate($data->endDateTime) : null;
            },
        ],
    ],
]);
?>
 <?php \yii\widgets\Pjax::end(); ?>
 </div>