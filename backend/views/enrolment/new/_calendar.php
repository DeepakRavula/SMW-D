<?php

use common\models\LocationAvailability;
use yii\helpers\Json;
use common\models\Lesson;
use common\models\TeacherAvailability;
use common\models\Program;
use yii\helpers\Url;
use common\models\Invoice;
?>
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<?php
    $locationId = Yii::$app->session->get('location_id');
    $minLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['fromTime' => SORT_ASC])
        ->one();
    $maxLocationAvailability = LocationAvailability::find()
        ->where(['locationId' => $locationId])
        ->orderBy(['toTime' => SORT_DESC])
        ->one();
    $from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');

	$teacherAvailabilities = TeacherAvailability::find()
		->joinWith(['userLocation' => function ($query) use ($programId, $locationId) {
			$query->joinWith(['userProfile' => function ($query) use ($programId) {
				$query->joinWith(['user' => function($query) use($programId) {
					$query->joinWith(['qualifications' => function($query) use($programId) {
						$query->andWhere(['program_id' => $programId]);
					}]);
				}]);
			}]);
			$query->andWhere(['location_id' => $locationId]);
		}])
		->all();

	$availableHours = [];
		foreach ($teacherAvailabilities as $teacherAvailability) {
			$availableHours[] = [
				'start' => $teacherAvailability->from_time,
				'end' => $teacherAvailability->to_time,
				'dow' => [$teacherAvailability->day],
				'className' => 'teacher-available',
				'rendering' => 'inverse-background',
			];
		}
    $lessons = [];
    $lessons = Lesson::find()
        ->joinWith(['course' => function ($query) use ($locationId, $programId) {
			$query->joinWith(['program' => function ($query) use($programId){
				$query->andWhere(['program.id' => $programId]);
			}]);
            $query->andWhere(['locationId' => $locationId]);
        }])
        ->andWhere(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED]])
		->notDeleted()
        ->all();
   $events = [];
    foreach ($lessons as &$lesson) {
        $toTime = new \DateTime($lesson->date);
        $length = explode(':', $lesson->duration);
        $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
        if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
            $title = $lesson->course->program->name.' ( '.$lesson->course->getEnrolmentsCount().' ) ';
        } else {
            $title = $lesson->enrolment->student->fullName.' ( '.$lesson->course->program->name.' ) ';
        }
        $class = null;
        if (!empty($lesson->proFormaInvoice)) {
            if (in_array($lesson->proFormaInvoice->status, [Invoice::STATUS_PAID, Invoice::STATUS_CREDIT])) {
                $class = 'proforma-paid';
            } else {
                $class = 'proforma-unpaid';
            }
        }
        $events[] = [
            'title' => $title,
            'start' => $lesson->date,
            'end' => $toTime->format('Y-m-d H:i:s'),
            'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
            'className' => $class,
        ];
    }
    unset($lesson);

?>
<style>
    .tab-content{
    padding:0 !important;
}
.box-body .fc{
    margin:0 !important;
}

</style>
<div class-p-10>
	<div id="enrolment-calendar"></div>
</div>
<script type="text/javascript">
$(document).ready(function(){
  $('#enrolment-calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'agendaWeek'
    },
	allDaySlot : false,
	slotDuration: '00:15:00',
	titleFormat: 'DD-MMM-YYYY, dddd',
    defaultView: 'agendaWeek',
    minTime: "<?php echo $from_time; ?>",
    maxTime: "<?php echo $to_time; ?>",
	selectConstraint: 'businessHours',
    eventConstraint: 'businessHours',
	businessHours: <?php echo Json::encode($availableHours); ?>,
	allowCalEventOverlap: true,
    overlapEventsSeparate: true,
    events: <?php echo Json::encode($events); ?>,
  });
 });
</script>