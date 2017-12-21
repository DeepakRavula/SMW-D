<?php

use common\models\Lesson;
use yii\helpers\Url;
use yii\grid\GridView;

?>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'id' => 'customer-lesson-listing',
    'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
    'dataProvider' => $lessonDataProvider,
    'options' => ['class' => 'col-md-12'],
    'summary' => false,
    'emptyText' => false,
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
                if ($data->isCompleted()) {
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
                if (!empty($data->invoice)) {
                    $status = $data->invoice->getStatus();
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
		[
			'label' => 'Present?',
			'value' => function ($data) {
				return $data->getPresent();
			},
		],
    ],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
