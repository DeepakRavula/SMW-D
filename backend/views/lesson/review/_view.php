<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>

	<?php
$columns = [
        [
        'label' => 'Date/Time',
        'attribute' => 'date',
        'format' => 'datetime',
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
        [
        'attribute' => 'duration',
        'value' => function ($model, $key, $index, $widget) {
            return (new \DateTime($model->duration))->format('H:i');
        },
        'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
        'contentOptions' => ['class' => 'kv-sticky-column'],
    ],
        [
        'label' => 'Conflict',
        'headerOptions' => ['class' => 'bg-light-gray'],
        'value' => function ($data) use ($conflicts) {
            if (!empty($conflicts[$data->id])) {
                return current($conflicts[$data->id]);
            }
        },
    ],
];
if (!$model->isBulkTeacherChange) {
array_push($columns,  [
    'class' => 'yii\grid\ActionColumn',
    'template' => '{edit}',
    'buttons' => [
        'edit' => function ($url, $model) {
            return  Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>', '#', [
                'id' => 'edit-button-' . $model->id,
                'class' => 'review-lesson-edit-button m-l-20'
            ]);
        },
    ],
]);
}

if ($courseModel->program->isGroup()){
array_push($columns,   [
    'class' => 'yii\grid\ActionColumn',
    'template' => '{edit}',
    'buttons' => [
        'edit' => function ($url, $model) {
            return  Html::a('<i class="fa fa-trash" aria-hidden="true"></i>', '#', [
                'id' => 'delete-button-' . $model->id,
                'class' => 'review-lesson-delete-button m-l-20'
            ]);
        },
    ],
]);
}
?>
	
<div class="box">
	<?php Pjax::begin([
        'id' => 'review-lesson-listing',
        'timeout' => 6000,
    ]) ?>
	<?= GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => 'No conflicts here! You are ready to confirm!',
    ]); ?>
<?php Pjax::end(); ?>
</div>
	
	