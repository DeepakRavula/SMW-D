<?php

use common\models\Lesson;
use yii\helpers\Url;
use yii\grid\GridView;

?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Lessons</h4>
</div>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
    'dataProvider' => $lessonDataProvider,
    'options' => ['class' => 'col-md-12'],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'columns' => [
        [
            'label' => 'Student Name',
            'value' => function ($data) {
                return !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
            },
        ],
        [
            'label' => 'Program Name',
            'value' => function ($data) {
                return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
            },
        ],
        [
            'label' => 'Lesson Status',
            'value' => function ($data) {
                $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                $currentDate = new \DateTime();

                if ($lessonDate <= $currentDate) {
                    $status = 'Completed';
                } else {
                    $status = 'Scheduled';
                }

                return $status;
            },
        ],
        [
            'label' => 'Invoice Status',
            'value' => function ($data) {
                $rootLesson = $data->getRootLesson();
                if (!empty($rootLesson->invoice)) {
                    $status = $rootLesson->invoice->getStatus();
                } else {
                    $status = 'Not Invoiced';
                }

                return $status;
            },
        ],
        [
            'label' => 'Date',
            'value' => function ($data) {
                return !empty($data->date) ? Yii::$app->formatter->asDate($data->date) : null;
            },
        ],
    ],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
