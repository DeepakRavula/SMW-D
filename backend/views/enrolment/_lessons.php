<?php

use yii\helpers\Url;
use yii\helpers\Json;
use common\models\Enrolment;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Location;
use common\models\Student;
use common\models\UserProfile;
use common\models\GroupLesson;
?>



<div class="private-lesson-index">
    <?php
    $columns = [	
	[
		'label' => 'Due Date',
		'value' => function ($data) use($model) {
			if ($data->isPrivate()) {
				$dueDate = $data->dueDate;
			} else {
			    $groupLesson = GroupLesson::findOne(['lessonId' => $data->id, 'enrolmentId' => $model->id]);
				$dueDate = $groupLesson->dueDate;
			}
			return $dueDate ? Yii::$app->formatter->asDate($dueDate) : null;
		},
		'group' => true,
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
	    	$total = $data->privateLesson->total ?? 0;
    		return Yii::$app->formatter->asCurrency(round($data->isPrivate() ? $total : $data->getGroupNetPrice($model), 2));
	    },
	],
	[
	    'label' => 'Owing',
		'attribute' => 'owing',
		'contentOptions' => ['class' => 'text-right'],
        'headerOptions' => ['class' => 'text-right'],
	    'value' => function ($data) use ($model) {
			if ($data->isPrivate()) {
				$owing = $data->privateLesson->balance ?? 0;
			} else {
			    $groupLesson = GroupLesson::findOne(['lessonId' => $data->id, 'enrolmentId' => $model->id]);
				$owing = $groupLesson->balance;
			}
		    return $owing ? Yii::$app->formatter->asBalance($owing) : null;
	    },
	],
    ];
    ?>
    <div class="grid-row-open">
    <?php yii\widgets\Pjax::begin(['id' => 'enrolment-lesson-index', 'timeout' => 6000,]); ?>
<?= GridView::widget([
	    'dataProvider' => $lessonDataProvider,
	    'options' => ['id' => 'student-lesson-grid'],
	    'rowOptions' => function ($model, $key, $index, $grid) {
		    $url = Url::to(['lesson/view', 'id' => $model->id]);

		    return ['data-url' => $url];
		},
		'options' => ['class' => 'col-md-12'],
	    'tableOptions' => ['class' => 'table table-condensed'],
	    'headerRowOptions' => ['class' => 'bg-light-gray'],
	    'summary' => false,
	    'emptyText' => false,
	    'columns' => $columns,
	]);

	?>
	<?php yii\widgets\Pjax::end(); ?>
    </div>
</div>