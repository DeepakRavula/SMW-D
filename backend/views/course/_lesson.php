<?php

use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="grid-row-open p-10">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php $columns = [
		[
			'label' => 'Date',
			'value' => function ($data) {
				$date = Yii::$app->formatter->asDate($data->date);
				$lessonTime = (new \DateTime($data->date))->format('H:i:s');

				return !empty($date) ? $date.' @ '.Yii::$app->formatter->asTime($lessonTime) : null;
			},
		],
		[
			'label' => 'Status',
			'value' => function ($data) {
				$status = null;
				if (!empty($data->status)) {
					return $data->getStatus();
				}

				return $status;
			},
		],
	];
     ?>   
    <?php echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
   		'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>