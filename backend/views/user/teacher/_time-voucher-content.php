<?php
use kartik\grid\GridView;
use common\models\Lesson;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
.table > tbody > tr > td, .table > tfoot > tr > td {
        padding: 0px;  
}
</style>
<div class="clearfix"></div>
<?php
if (!$searchModel->summariseReport) {
$columns = [
        [
        'value' => function ($data) {
            if (! empty($data->date)) {
                $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                return $lessonDate->format('l, F jS, Y');
            }

            return null;
        },
        'group' => true,
        'groupedRow' => true,
        'groupFooter' => function ($model, $key, $index, $widget) {
            return [
                'mergeColumns' => [[1, 3]],
                'content' => [
                    4 => GridView::F_SUM,
                ],
                'contentFormats' => [
                    4 => ['format' => 'number', 'decimals' => 2],
                ],
                'contentOptions' => [
                    4 => ['style' => 'text-align:right'],
                ],
            'options'=>['style'=>'font-weight:bold;']
            ];
        }
    ],
        [
        'label' => 'Time',
        'width' => '250px',
        'value' => function ($data) {
            return !empty($data->date) ? Yii::$app->formatter->asTime($data->date) : null;
        },
    ],
        [
        'label' => 'Program',
        'width' => '250px',
        'value' => function ($data) {
            return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
        },
    ],
        [
        'label' => 'Student',
        'value' => function ($data) {
            $student = ' - ';
            if ($data->course->program->isPrivate()) {
                $student = !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
            }
            return $student;
        },
    ],
        [
        'label' => 'Duration(hrs)',
        'value' => function ($data) {
            return $data->getDuration();
        },
        'contentOptions' => ['class' => 'text-right'],
            'hAlign'=>'right',
            'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
    ],
];
        } else {

        $columns = [
                [
        'label' => 'Date',
        'value' => function ($data) {
            if (! empty($data->date)) {
                $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
                return $lessonDate->format('l, F jS, Y');
            }

            return null;
        },
            
    ],
        		[
			'label' => 'Duration(hrs)',
			'value' => function ($data){
				$locationId = Yii::$app->session->get('location_id');
				$lessons =Lesson::find()
            ->innerJoinWith('enrolment')
            ->location($locationId)
            ->notDeleted()
            ->scheduledOrRescheduled()
            ->isConfirmed()
            ->andWhere(['DATE(date)' => (new \DateTime($data->date))->format('Y-m-d'), 'lesson.teacherId' => $data->teacherId])
					->all();
				$totalDuration = 0;
				foreach($lessons as $lesson) {
					$duration		 = \DateTime::createFromFormat('H:i:s', $lesson->duration);
					$hours			 = $duration->format('H');
					$minutes		 = $duration->format('i');
					$lessonDuration	 = $hours + ($minutes / 60);
					$totalDuration += $lessonDuration;
				}
				return $totalDuration;
			},
			'contentOptions' => ['class' => 'text-right'],
			'hAlign'=>'right',
			'pageSummary'=>true,
            'pageSummaryFunc'=>GridView::F_SUM
		],
            ];
        }
?>
<?=
GridView::widget([
    'dataProvider' => $teacherLessonDataProvider,
        'summary' => false,
        'emptyText' => false,
    'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'pjax' => true,
    'showPageSummary' => true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'teacher-lesson-grid',
        ],
    ],
    'columns' => $columns,
]);
?>