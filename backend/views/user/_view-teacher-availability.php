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
				return ! empty($data->from_time) ? Yii::$app->formatter->asTime($data->from_time) : null;
			}
		],
		[
			'label' => 'To Time',
			'value' => function($data) {
				return ! empty($data->to_time) ? Yii::$app->formatter->asTime($data->to_time) : null;
			}
		],
		['class' => 'yii\grid\ActionColumn', 'controller' => 'teacher-availability', 'template' => '{delete}'],
	],
]);
?>
<div class="col-md-12 m-b-20 m-t-20">
		<?php echo Html::a('<i class="fa fa-pencil"></i> Update Availability', ['update','UserSearch[role_name]' => $searchModel->role_name,'id' => $model->id,'section' => 'availability'], ['class' => 'm-r-20']) ?>
</div>