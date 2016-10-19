<?php
use common\models\Location;
use common\models\TeacherAvailability;
use common\models\Lesson;
use common\models\Program;
use yii\helpers\Json;
use yii\helpers\Url;
?>
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
						$query->where(['teacherId' => '470'])
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
			->where(['lesson.teacherId' => '470'])
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
            $events[]= [
                'resources' => $lesson->teacherId,
                'title' => $title,
                'start' => $lesson->date,
                'end' => $toTime->format('Y-m-d H:i:s'),
            ];
        }
        unset($lesson);
?>
<div class="schedule-index">
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
	allowCalEventOverlap: true,
    overlapEventsSeparate: true,
    resources:  <?php echo Json::encode($teachersWithClass); ?>,
    events: <?php echo Json::encode($events); ?>,
	select: function(start, end, allDay) {
//		$('#course-detail').show();
		$('#course-day').val(moment(start).format('d'));
		$('#course-fromtime').val(moment(start).format('h:mm A'));
		$('#course-startdate').val(moment(start).format('DD-MM-YYYY'));
		$('#calendar').fullCalendar('unselect');
	},
    selectable: true,
    selectHelper: true,
  });
});
</script>