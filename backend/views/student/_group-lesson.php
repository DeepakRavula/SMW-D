<?php

use yii\helpers\Url;
use yii\helpers\Json;
use common\models\Enrolment;
use kartik\grid\GridView;
use common\models\Lesson;

?>

<div class="group-lesson-index">
    <?php $columns = [
            [
                'label' => 'Due Date',
                'value' => function ($data) {
                    return $data->groupLesson->dueDate ? Yii::$app->formatter->asDate($data->groupLesson->dueDate) : null;
                },
                'group' => true,
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
                    return $data->groupLesson->dueDate ? Yii::$app->formatter->asDate($data->groupLesson->dueDate) : null;
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
                        ->student($model->id)
                        ->one();
                    return Yii::$app->formatter->asCurrency(round($data->getGroupNetPrice($enrolment), 2));
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
                        ->student($model->id)
                        ->one();
                    return Yii::$app->formatter->asCurrency($data->getOwingAmount($enrolment->id));
                },
            ],
        ];

    ?>
    <div class="grid-row-open">
    <?php yii\widgets\Pjax::begin(['id' => 'group-lesson-index', 'timeout' => 6000,]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $groupLessonDataProvider,
        'options' => ['id' => 'student-group-lesson-grid'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => false,
        'emptyText' => false,
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>
    </div>
</div>