<?php

use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Lesson;
use yii\data\ActiveDataProvider;
use common\models\Holiday;
use common\models\ProfessionalDevelopmentDay;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php
$this->registerJs("
    $('.private-lesson-index td').click(function (e) {
        var id = $(this).closest('tr').data('id');
        if(e.target == this)
            location.href = '" . Url::to(['lesson/view'],['class' => 'private-lesson-index','target' => '_blank']) . "?id=' + id;
    });

");
?>
<?php
	$conflicts = current($conflicts);
	$conflictedLessonIds = [];
	$conflictedDates = [];
	foreach($conflicts['lessonIds'] as $lessonConflict){
		$conflictedLessonIds[] = $lessonConflict;
	}
	foreach($conflicts['dates'] as $dateConflict){
		$conflictedDates[] = $dateConflict;
	}
	$holidays = Holiday::find()
		->all();
	$professionalDevelopmentDays = ProfessionalDevelopmentDay::find()
		->all();
	
	$results = [];
	foreach($holidays as $holiday){
		foreach($conflictedDates as $conflictedDate){
			$holidayDate = \DateTime::createFromFormat('Y-m-d H:i:s', $holiday->date);
			$holidayDate = $holidayDate->format('Y-m-d'); 
			if(new \DateTime($holidayDate) == new \DateTime($conflictedDate)){
				$results[] = [
					'date' => $holiday->date,
					'type' => 'Holiday',
				];
			}
		}
	}
	foreach($professionalDevelopmentDays as $professionalDevelopmentDay){
		foreach($conflictedDates as $conflictedDate){
			$professionalDevelopmentDayDate = \DateTime::createFromFormat('Y-m-d H:i:s', $professionalDevelopmentDay->date);
			$professionalDevelopmentDayDate = $professionalDevelopmentDayDate->format('Y-m-d'); 
			$lessonDate = new \DateTime($conflictedDate);
			if(new \DateTime($professionalDevelopmentDayDate) == new \DateTime($conflictedDate)){
				$results[] = [
					'date' => $professionalDevelopmentDay->date,
					'type' => 'Professional Development Day',
				];
			}
		}
	}
	$conflictedLessonDataProvider = new ActiveDataProvider([
		'query' => Lesson::find()
			->where(['IN', 'id', $conflictedLessonIds]),
	]);	
?>
<div class="private-lesson-index p-10">
	<?php if($conflictedLessonDataProvider->getCount() > 0) :?>
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php $columns = [
			'id',
			[
				'label' => 'Teacher Name',
				'value' => function($data) {
					return $data->teacher->publicIdentity;
					}
			],
			[
				'label' => 'Program Name',
				'value' => function($data) {
					return $data->course->program->name;
                },
			],
			[
				'label' => 'Date',
				'value' => function($data) {
					$date = Yii::$app->formatter->asDate($data->date); 
					return ! empty($date) ? $date : null;
                },
			],
			[
				'label' => 'Time',
				'value' => function($data) {
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					$fromTime = $lessonDate->format('H:i:s');
					$length = explode(':', $data->course->duration);
					$lessonDate->add(new DateInterval('PT' . $length[0] . 'H' . $length[1] . 'M'));
					$toTime = $lessonDate->format('H:i:s');
					return  Yii::$app->formatter->asTime($fromTime) . ' to ' . Yii::$app->formatter->asTime($toTime);
				},
			],
			[
				'label' => 'Type',
				'value' => function($data) {
					if(empty($data->course->enrolment->id)){
						return 'Teacher\'s Lesson';
					}
					return 'Own Lesson';
                },
			],
        ];
     ?>   
    <?php echo GridView::widget([
        'dataProvider' => $conflictedLessonDataProvider,
		'rowOptions' => function ($model, $key, $index, $grid) {
            return ['data-id' => $model->id];
        },
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>
<?php elseif( ! empty($results)):?>
	<?php
		foreach($results as $result){
			echo 'Date: ' . Yii::$app->formatter->asDate($result['date']) . '  ';
			echo 'Type: ' . $result['type'];
		}
	?>
<?php else:?>
	<?= 'No conflict'; ?>
<?php endif;?>
</div>