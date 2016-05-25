<?php

use yii\helpers\Html;
use common\models\Enrolment;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Enrolment */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Enrolments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="enrolment-view">
<?php
	$dayList = Enrolment::getWeekdaysList();
	$day = $dayList[$model->enrolmentScheduleDay->day];
	$fromTime = date("g:i a",strtotime($model->enrolmentScheduleDay->from_time));
	$duration = date("g:i",strtotime($model->enrolmentScheduleDay->duration));
	$date = $model->commencement_date;
	$commencement_date = date('d-m-Y',strtotime($date));
	$renewalDate = $model->renewal_date;
	$renewal_date = date('d-m-Y',strtotime($renewalDate));
?>
<?php
	echo DetailView::widget([
		'model' => $model,
		'attributes' => [
			[
				'label' => 'Program Name',
				'value' => !empty($model->qualification->program->name) ? $model->qualification->program->name : null,
			],
			[
				'label' => 'Teacher Name',
				'value' => !empty($model->qualification->teacher->publicIdentity) ? $model->qualification->teacher->publicIdentity : null,
			],
			[
				'label' => 'Day',
				'value' => ! empty($day) ? $day : null,
			],
			[
				'label' => 'From Time',
				'value' => !empty($fromTime) ? $fromTime : null,
			],
			[
				'label' => 'Duration',
				'value' => !empty($duration) ? $duration : null,
			],
			[
				'label' => 'Commencement Date',
				'value' => ! empty($commencement_date) ? $commencement_date : null,	
			],
			[
				'label' => 'Renewal Date',
				'value' => ! empty($renewal_date) ? $renewal_date : null,
			],
		],
	])
	?>
    <p>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
</div>
