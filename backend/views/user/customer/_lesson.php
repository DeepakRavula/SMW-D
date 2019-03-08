<?php

use common\models\Lesson;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Enrolment;

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
            'label' => 'Date',
            'value' => function ($data) {
                return Yii::$app->formatter->asDate($data->date) . ' @ ' . Yii::$app->formatter->asTime($data->date);
            },
        ],
        [
            'label' => 'Duration',
            'value' => function ($data) {
                $lessonDuration = (new \DateTime($data->duration))->format('H:i');
                return $lessonDuration;
            },
        ],
        [
            'label' => 'Due Date',
            'value' => function ($data) {
                return $data->dueDate ? Yii::$app->formatter->asDate($data->dueDate) : null;
            },
        ],
        [
            'label' => 'Status',
            'value' => function ($data) {
                return $data->getStatus();
            },
        ],
        [
            'label' => 'Price',
            'attribute' => 'price',
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($model) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $data->courseId])
                    ->customer($model->id)
                    ->one();
                return Yii::$app->formatter->asCurrency(round($data->isPrivate() ? $data->privateLesson->total : $data->getGroupNetPrice($enrolment), 2));
            },
        ],
        [
            'label' => 'Owing',
            'attribute' => 'owing',
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($model) {
                $enrolment = Enrolment::find()
                    ->notDeleted()
                    ->isConfirmed()
                    ->andWhere(['courseId' => $data->courseId])
                    ->customer($model->id)
                    ->one();
                if ($data->isPrivate()) {
                    $enrolment = $data->enrolment;
                }
                return Yii::$app->formatter->asCurrency($data->privateLesson->balance);
            },
        ],
    ],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
