<?php

use yii\helpers\Url;
use yii\helpers\Json;
use common\models\Enrolment;
use yii\grid\GridView;
?>



<div class="private-lesson-index">
    <?php
    $columns = [
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
		    $duration = substr($lessonDuration, 3);
		    return $duration . "mins";
	    },
	],
	[
	    'label' => 'Price',
	    'attribute' => 'price',
	    'value' => function ($data) {
		    return Yii::$app->formatter->asCurrency($data->netPrice);
	    },
	],
	[
	    'label' => 'Lesson Status',
	    'value' => function ($data) {
		    return $data->getStatus();
	    },
	],
	[
	    'label' => 'Invoice Status',
	    'value' => function ($data) {
		    return $data->getInvoiceStatus();
	    },
	],
	[
	    'label' => 'Present',
	    'value' => function ($data) {
		    return $data->getPresent();
	    },
	],
    ];
    ?>
    <div class="grid-row-open">
    <?php yii\widgets\Pjax::begin(['id' => 'enrolment-lesson-index', 'timeout' => 6000,]); ?>
	<?php
	echo GridView::widget([
	    'dataProvider' => $lessonDataProvider,
	    'options' => ['id' => 'student-lesson-grid'],
	    'rowOptions' => function ($model, $key, $index, $grid) {
		    $url = Url::to(['lesson/view', 'id' => $model->id]);

		    return ['data-url' => $url];
	    },
	    'tableOptions' => ['class' => 'table table-bordered'],
	    'headerRowOptions' => ['class' => 'bg-light-gray'],
	    'summary' => false,
	    'emptyText' => false,
	    'columns' => $columns,
	]);
	?>
	<?php yii\widgets\Pjax::end(); ?>
    </div>
</div>