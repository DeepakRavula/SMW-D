<?php
use common\models\Location;
use common\models\TeacherAvailability;
use common\models\Lesson;
use common\models\Program;
use yii\helpers\Json;
use yii\helpers\Url;
?>
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<?php
$location = Location::findOne($id=Yii::$app->session->get('location_id'));
	$from_time = (new \DateTime($location->from_time))->format('H:i:s');
	$to_time = (new \DateTime($location->to_time))->format('H:i:s');
	$locationId			 = Yii::$app->session->get('location_id');
		$teachersWithClass	 = TeacherAvailability::find()
			->select(['user_location.user_id as id', "CONCAT(user_profile.firstname, ' ', user_profile.lastname) as name"])
			->distinct()
			->joinWith(['userLocation' => function($query) use($locationId, $model) {
				$query->joinWith(['userProfile' => function($query) use($model){
					$query->joinWith(['lesson' => function($query) use($model){
						$query->where(['teacherId' => '636'])
							->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]]);
					}]);
				}])
				->where(['user_location.location_id' => $locationId]);
			}])
			->orderBy(['teacher_availability_day.id' => SORT_DESC])
			->one();

		$activeTeachers = [
			'id' => $teachersWithClass->id,
			'name' => $teachersWithClass->name,
		];

		$lessons =[];
        $lessons = Lesson::find()
			->joinWith(['course' => function($query) {
			    $query->andWhere(['locationId' => Yii::$app->session->get('location_id')]);
			}])
			->where(['lesson.teacherId' => '636'])
            ->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
            ->all();
       $events = [];
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->duration);
		    $toTime->add(new \DateInterval('PT' . $length[0] . 'H' . $length[1] . 'M'));
            
            $events[]= [
                'start' => $lesson->date,
                'end' => $toTime->format('Y-m-d H:i:s'),
				'className' => 'teacher-lesson'
            ];
        }
        unset($lesson);

	$teacherAvailabilityDays = TeacherAvailability::find()
		->joinWith(['userLocation' => function($query) {
			$query->joinWith(['userProfile' => function($query){
				$query->where(['user_profile.user_id' => '636']);
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
?>
<div class="calendar">
<div id='calendar' class="p-10"></div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

  $('#calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'agendaWeek,resourceDay'
    },
	titleFormat: 'DD-MMM-YYYY, dddd',
    defaultView: 'agendaWeek',
    minTime: "<?php echo $from_time; ?>",
    maxTime: "<?php echo $to_time; ?>",
    slotDuration: "00:15:01",
	selectConstraint: 'businessHours',
    eventConstraint: 'businessHours',
	businessHours: <?php echo Json::encode($availableHours); ?>,
	allowCalEventOverlap: true,
    overlapEventsSeparate: true,
    resources:  <?php echo Json::encode($teachersWithClass); ?>,
    events: <?php echo Json::encode($events); ?>,
	select: function(start, end, allDay) {
		$('#calendar').fullCalendar('removeEvents', 'newEnrolment');
		$('#course-day').val(moment(start).format('dddd'));
		$('#course-fromtime').val(moment(start).format('h:mm A'));
		$('#course-startdate').val(moment(start).format('DD-MM-YYYY'));
		var title = 1;
		if (title) {
			$('#calendar').fullCalendar('renderEvent',
				{
					id : 'newEnrolment',
					title: title,
					start: start,
					end: end,
					allDay: false
				},
				true // make the event "stick"
			);
		}
	},
    selectable: true,
    selectHelper: true,
  });
});
</script>