<?php
use yii\grid\GridView;
use common\models\Enrolment;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Enrolments</h4>
</div>
<?php yii\widgets\Pjax::begin() ?>
<?php
	echo GridView::widget([
		'dataProvider' => $enrolmentDataProvider,
		'options' => ['class' => 'col-md-12'],
		'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
		'columns' => [
			[
				'label' => 'Student Name',
				'value' => function($data) {
					return !empty($data->student->fullName) ? $data->student->fullName : null;
				},
			],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return !empty($data->program->name) ? $data->program->name : null;
				},
			],
			[
				'label' => 'Teacher Name',
				'value' => function($data) {
					return !empty($data->lessons[0]->teacher->publicIdentity) ? $data->lessons[0]->teacher->publicIdentity : null;
				},
			],
			[
				'label' => 'Day',
				'value' => function($data) {
					if(! empty($data->day)){
					$dayList = Enrolment::getWeekdaysList();
					$day = $dayList[$data->day];
					return ! empty($day) ? $day : null;
					}
					return null;
				},
			],
			[
				'label' => 'From Time',
				'value' => function($data) {
					if(! empty($data->from_time)){
						return ! empty($data->from_time) ? Yii::$app->formatter->asTime($data->from_time) : null;
					}
					return null;
				},
			],
			[
				'label' => 'Duration',
				'value' => function($data) {
					if(! empty($data->duration)){
                    	$duration = \DateTime::createFromFormat('h:i:s',$data->duration);
       				    $data->duration = $duration->format('H:i');
                    	return !empty($data->duration) ? $data->duration : null;
					}
					return null;
				},
			],
			[
				'label' => 'Commencement Date',
				'value' => function($data) {
					return ! empty($data->commencement_date) ? Yii::$app->formatter->asDate($data->commencement_date) : null;
				},
			],
			[
				'label' => 'Renewal Date',
				'value' => function($data) {
					return ! empty($data->renewal_date) ? Yii::$app->formatter->asDate($data->renewal_date) : null;
				},
			],
		],
	]);
	?>
<?php \yii\widgets\Pjax::end(); ?>
