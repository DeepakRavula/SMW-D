<?php
use yii\grid\GridView;
use common\models\Enrolment;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Enrolments</h4>
</div>
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
					return !empty($data->program->qualification->teacher->publicIdentity) ? $data->program->qualification->teacher->publicIdentity : null;
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
						$fromTime = date("g:i a",strtotime($data->from_time));
						return !empty($fromTime) ? $fromTime : null;
					}
					return null;
				},
			],
			[
				'label' => 'Duration',
				'value' => function($data) {
					if(! empty($data->duration)){
                    	$duration = date("H:i",strtotime($data->duration));
                    	return !empty($duration) ? $duration : null;
					}
					return null;
				},
			],
			[
				'label' => 'Commencement Date',
				'value' => function($data) {
					if(!empty($data->commencement_date)){
					$date = $data->commencement_date;
					$commencement_date = date('d-m-Y',strtotime($date));
					return ! empty($commencement_date) ? $commencement_date : null;
					}
					return null;
				},
			],
			[
				'label' => 'Renewal Date',
				'value' => function($data) {
					if(!empty($data->renewal_date)){
					$date = $data->renewal_date;
					$renewal_date = date('d-m-Y',strtotime($date));
					return ! empty($renewal_date) ? $renewal_date : null;
					}
					return null;
				},
			],
		],
	]);
	?>
