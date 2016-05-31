<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\Enrolment;
use common\models\User;
use common\models\Student;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Student Details';
//$this->title = $model->studentIdentity;
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-details-wrapper">
	<div class="col-md-12">
		<p class="users-name"><?php echo $model->first_name; ?> <?php echo $model->last_name; ?></p>
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Birth date">
		<i class="fa fa-birthday-cake detail-icon"></i> <?php echo $model->birth_date; ?>
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Customer">
		<i class="fa fa-user detail-icon"></i> <?php echo !empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null ?>
	</div>
	<div class="clearfix"></div>
	<div class="student-view">
		<?php
		// echo DetailView::widget([
		// 	'model' => $model,
		// 	'attributes' => [
		// 		'first_name',
		// 		'last_name',
		// 		'birth_date:date',
		// 		[
		// 			'label' => 'Customer Name',
		// 			'value' => !empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null,
		// 		],
		// 	],
		// ])
		?>
		<div class="col-md-12 action-btns">
			<?php echo Html::a('<i class="fa fa-pencil"></i> Update details', ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
			<?php
			echo Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->id], [
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				],
			])
			?>
	    </div>
	    <div class="clearfix"></div>
	</div>
</div>
</div>
<div class="col-md-12">
<h4 class="pull-left m-r-20">Program details</h4>
<a href="#" class="add-new-program text-add-new"><i class="fa fa-plus-circle"></i> Add new program</a>
<div class="clearfix"></div>
</div>

<div class="dn enrolment-create">
    <?php echo $this->render('//enrolment/_form', [
        'model' => $enrolmentModel,
    ]) ?>
</div>
<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider,
		'options' => ['class' => 'col-md-12'],
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
<div class="clearfix"></div>
<script>
	$('.add-new-program').click(function(){
		$('.enrolment-create').show(); 
	});
</script>
