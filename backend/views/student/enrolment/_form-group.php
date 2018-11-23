<?php

use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use common\models\Course;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div id="course-spinner" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>    
<div class="user-create-index"> 
    <?php echo GridView::widget([
        'dataProvider' => $groupDataProvider,
        'tableOptions' => ['class' => 'table table-condensed'],
        'rowOptions' => ['class' => 'group-enrol-btn'],
        'summary' => false,
        'emptyText' => false,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'Course',
                'value' => function ($data) {
                    return !empty($data->program->name) ? $data->program->name : null;
                },
            ],
            [
                'label' => 'Teacher',
                'value' => function ($data) {
                    return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
                },
            ],
            [
                'label' => 'Day',
                'value' => function ($data) {
                    $dayList = Course::getWeekdaysList();
                    $day = $dayList[$data->recentCourseSchedule->day];

                    return !empty($day) ? $day : null;
                },
            ],
            [
                'attribute' => 'rate',
                'label' => 'Rate',
                'value' => function ($data) {
                    return !empty($data->program->rate) ? $data->program->rate : null;
                },
            ],
            [
                'label' => 'From Time',
                'value' => function ($data) {
                    return Yii::$app->formatter->asTime($data->startDate);
                },
            ],
            [
                'label' => 'Duration',
                'value' => function ($data) {
                    $length = \DateTime::createFromFormat('H:i:s', $data->recentCourseSchedule->duration);

                    return !empty($data->recentCourseSchedule->duration) ? $length->format('H:i') : null;
                },
            ],
            [
                'label' => 'Start Date',
                'value' => function ($data) {
                    return !empty($data->startDate) ? Yii::$app->formatter->asDate($data->startDate) : null;
                },
            ],
            [
                'label' => 'End Date',
                'value' => function ($data) {
                    return !empty($data->endDate) ? Yii::$app->formatter->asDate($data->endDate) : null;
                },
            ],
        ],
    ]); ?>
</div>
