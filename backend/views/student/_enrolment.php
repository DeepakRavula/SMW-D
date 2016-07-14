<?php

use yii\grid\GridView;
use common\models\Enrolment;
?>
<div class="col-md-12">
<h4 class="pull-left m-r-20">Enrolments</h4>
<a href="#" class="add-new-program text-add-new"><i class="fa fa-plus"></i></a>
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
						return ! empty($data->from_time) ? Yii::$app->formatter->asTime($data->from_time) : null;
				}
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
					return ! empty($data->commencement_date) ? Yii::$app->formatter->asDate($data->commencement_date) : null;

				}
			],
			[
				'label' => 'Renewal Date',
				'value' => function($data) {
					return ! empty($data->renewal_date) ? Yii::$app->formatter->asDate($data->renewal_date) : null;

				}
			],
		],
	]);
	?>
