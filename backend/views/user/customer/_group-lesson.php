<?php
use common\models\Lesson;
use yii\helpers\Url;
use kartik\grid\GridView;
use common\models\Enrolment;
?>

<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
    'id' => 'customer-lesson-listing',
    'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
    'dataProvider' => $groupLessonDataProvider,
    'options' => ['class' => 'col-md-12', 'id' => 'lesson-listing-customer-view'],
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
            'label' => 'Due Date',
            'value' => function ($data) {
                return Yii::$app->formatter->asDate($data->dueDate);
            },
            'group' => true,
        ],
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
                return $data->lesson->date ? Yii::$app->formatter->asDate($data->lesson->date) : null;
            },
        ],
        [
            'label' => 'Duration',
            'value' => function ($data) {
                $lessonDuration = (new \DateTime($data->lesson->duration))->format('H:i');
                return $lessonDuration;
            },
        ],
        [
            'label' => 'Status',
            'value' => function ($data) {
                return $data->lesson->getStatus();
            },
        ],
        [
            'label' => 'Price',
            'attribute' => 'price',
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions' => ['class' => 'text-right'],
            'value' => function ($data) {
                return Yii::$app->formatter->asCurrency(round($data->total, 2));
            },
        ],
        [
            'label' => 'Owing',
            'attribute' => 'owing',
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions' => ['class' => 'text-right'],
            'value' => function ($data) use ($model) {
                return Yii::$app->formatter->asCurrency(round($data->balance, 2));
            },
        ],
    ],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>
