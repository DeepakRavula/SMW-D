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
    ],
];
?>
	
<div class="box">
	<?php Pjax::begin([
        'id' => 'review-unscheduled-lesson-listing',
        'timeout' => 6000,
    ]) ?>
	<?= GridView::widget([
        'dataProvider' => $unscheduledLessonDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => 'No Unscheduled Lessons',
    ]); ?>
<?php Pjax::end(); ?>
</div>