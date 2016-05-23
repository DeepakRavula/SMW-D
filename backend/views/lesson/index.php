<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lessons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-index">




    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			[
				'label' => 'Student Name',
				'value' => function($data) {
					return ! empty($data->enrolmentScheduleDay->enrolment->student->fullName) ? $data->enrolmentScheduleDay->enrolment->student->fullName : null;
                },
			],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return ! empty($data->enrolmentScheduleDay->enrolment->qualification->program->name) ? $data->enrolmentScheduleDay->enrolment->qualification->program->name : null;
                },
			],	
		[
				'label' => 'Status',
				'value' => function($data) {
					switch($data->status){
						case Lesson::STATUS_COMPLETED:
							$status = 'Completed';
						break;
						case Lesson::STATUS_PENDING:
							$status = 'Pending';
						break;
						case Lesson::STATUS_CANCELED:
							$status = 'Canceled';
						break;
					}
					return $status;
                },
			],
			[
				'label' => 'Date',
				'value' => function($data) {
					$date = date("d-m-y", strtotime($data->date)); 
					return ! empty($date) ? $date : null;
                },
			],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
