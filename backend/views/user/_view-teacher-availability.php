<?php

use yii\grid\GridView;
use common\models\TeacherAvailability;
use yii\helpers\Html;

?>
<?php

echo GridView::widget([
	'dataProvider' => $teacherDataProvider,
	'options' => ['class' => 'col-md-5'],
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'columns' => [
		['class' => 'yii\grid\SerialColumn'],
		[
			'label' => 'Day',
			'value' => function($data) {
				if (!empty($data->day)) {
					$dayList = TeacherAvailability::getWeekdaysList();
					$day = $dayList[$data->day];
					return !empty($day) ? $day : null;
				}
				return null;
			},
		],
		[
			'label' => 'From Time',
			'value' => function($data) {
				if (!empty($data->from_time)) {
					$fromTime = date("g:i a", strtotime($data->from_time));
					return !empty($fromTime) ? $fromTime : null;
				}
				return null;
			},
		],
		[
			'label' => 'To Time',
			'value' => function($data) {
				if (!empty($data->to_time)) {
					$toTime = date("g:i a", strtotime($data->to_time));
					return !empty($toTime) ? $toTime : null;
				}
				return null;
			},
		],
		['class' => 'yii\grid\ActionColumn', 'controller' => 'teacher-availability', 'template' => '{delete}'],
	],
]);
?>
<div class="m-t-20">
		<?php echo Html::a('<i class="fa fa-pencil"></i> Update Availability', ['update', 'id' => $model->id,'section' => 'availability'], ['class' => 'm-r-20']) ?>
</div>