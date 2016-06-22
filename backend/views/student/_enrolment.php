<?php

use yii\grid\GridView;
use common\models\Enrolment;
?>
<div class="col-md-12">
<h4 class="pull-left m-r-20">Enrolments</h4>
<a href="#" class="add-new-program text-add-new"><i class="fa fa-plus-circle"></i> Add new program</a>
<div class="clearfix"></div>
</div>

<div class="dn enrolment-create section-tab">
    <?php echo $this->render('//enrolment/_form', [
        'model' => $enrolmentModel,
    ]) ?>
</div>
<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'options' => ['class' => 'col-md-12'],
		'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return !empty($data->qualification->program->name) ? $data->qualification->program->name : null;
				},
			],
			[
				'label' => 'Teacher Name',
				'value' => function($data) {
					return !empty($data->qualification->teacher->publicIdentity) ? $data->qualification->teacher->publicIdentity : null;
				},
			],
			[
				'label' => 'Day',
				'value' => function($data) {
					if(! empty($data->enrolmentScheduleDay->day)){
					$dayList = Enrolment::getWeekdaysList();
					$day = $dayList[$data->enrolmentScheduleDay->day];
					return ! empty($day) ? $day : null;
					}
					return null;
				},
			],
			[
				'label' => 'From Time',
				'value' => function($data) {
					if(! empty($data->enrolmentScheduleDay->from_time)){
						$fromTime = date("g:i a",strtotime($data->enrolmentScheduleDay->from_time));
						return !empty($fromTime) ? $fromTime : null;
					}
					return null;
				},
			],
			[
				'label' => 'Duration',
				'value' => function($data) {
					if(! empty($data->enrolmentScheduleDay->duration)){
                    	$duration = date("H:i",strtotime($data->enrolmentScheduleDay->duration));
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
			['class' => 'yii\grid\ActionColumn', 'controller' => 'enrolment','template' => '{delete}'],
		],
	]);
	?>
