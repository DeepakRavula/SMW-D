<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\Enrolment;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Student */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-view">


	<?php
	echo DetailView::widget([
		'model' => $model,
		'attributes' => [
			'first_name',
			'last_name',
			'birth_date:date',
			[
				'label' => 'Customer Name',
				'value' => !empty($model->customer->userProfile->fullName) ? $model->customer->userProfile->fullName : null,
			],
		],
	])
	?>
	<p>
		<?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
		<?php
		echo Html::a('Delete', ['delete', 'id' => $model->id], [
			'class' => 'btn btn-danger',
			'data' => [
				'confirm' => 'Are you sure you want to delete this item?',
				'method' => 'post',
			],
		])
		?>
    </p>

</div>
<?php
	echo GridView::widget([
		'dataProvider' => $dataProvider,
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
					return !empty($data->qualification->user->userProfile->fullName) ? $data->qualification->user->userProfile->fullName : null;
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
					return !empty($data->enrolmentScheduleDay->duration) ? $data->enrolmentScheduleDay->duration : null;
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
			['class' => 'yii\grid\ActionColumn', 'controller' => 'enrolment','template' => '{view} {delete}'],
		],
	]);
	?>
<div class="enrolment-create">

    <?php echo $this->render('//enrolment/_form', [
        'model' => $enrolmentModel,
    ]) ?>

</div>
