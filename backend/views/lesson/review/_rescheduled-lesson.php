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
];
?>
	
<div class="box">
	<?php Pjax::begin([
        'id' => 'review-rescheduled-lesson-listing',
        'timeout' => 6000,
    ]) ?>
	<?= GridView::widget([
        'dataProvider' => $rescheduledLessonDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => 'No Rescheduled Lessons',
    ]); ?>
<?php Pjax::end(); ?>
</div>