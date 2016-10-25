<?php
use common\models\Location;
use common\models\TeacherAvailability;
use common\models\Lesson;
use common\models\Program;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\Invoice;
?>
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<?php
	$locationId			 = Yii::$app->session->get('location_id');
	$location = Location::findOne(['id' => $locationId]);
	$from_time = (new \DateTime($location->from_time))->format('H:i:s');
	$to_time = (new \DateTime($location->to_time))->format('H:i:s');

	$teacherAvailabilityDays = TeacherAvailability::find()
		->joinWith(['userLocation' => function($query) use($teacherId) {
			$query->joinWith(['userProfile' => function($query) use($teacherId){
				$query->where(['user_profile.user_id' => $teacherId]);
			}]);
		}])
		->all();
		$availableHours = [];
	foreach($teacherAvailabilityDays as $teacherAvailabilityDay) {
		$availableHours[] = [
			'start' => $teacherAvailabilityDay->from_time,
			'end' => $teacherAvailabilityDay->to_time,
			'dow' => [$teacherAvailabilityDay->day],
			'className' => 'teacher-available'
		];
	}

	$lessons = [];
	$lessons = Lesson::find()
		->joinWith(['course' => function($query) use($locationId) {
			$query->andWhere(['locationId' => $locationId]);
		}])
		->where(['lesson.teacherId' =>  $teacherId])
		->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
		->all();
   $events = [];
	foreach ($lessons as &$lesson) {
		$toTime = new \DateTime($lesson->date);
		$length = explode(':', $lesson->duration);
		$toTime->add(new \DateInterval('PT' . $length[0] . 'H' . $length[1] . 'M'));
		if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                $title = $lesson->course->program->name . ' ( ' . $lesson->course->getEnrolmentsCount() . ' ) ';
            } else {
            	$title = $lesson->enrolment->student->fullName . ' ( ' .$lesson->course->program->name . ' ) ';
		}
		$class = null;
		if (! empty($lesson->proFormaInvoice)) {
			if (in_array($lesson->proFormaInvoice->status, [Invoice::STATUS_PAID, Invoice::STATUS_CREDIT])) {
				$class = 'proforma-paid';
			} else {
				$class = 'proforma-unpaid';
			}
		}
		$events[]= [
            'title' => $title,
			'start' => $lesson->date,
			'end' => $toTime->format('Y-m-d H:i:s'),
            'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
			'className' => $class,
		];
	}
	unset($lesson);

?>
<div class="calendar">
<div id="calendar" class="p-10"></div>
</div>
<script type="text/javascript">
  $('#calendar').fullCalendar({
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
</script>