<?php

namespace backend\controllers;

use Yii;
use common\models\Location;
use common\models\Lesson;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\filters\AccessControl;
use common\models\TeacherAvailability;
/**
 * QualificationController implements the CRUD actions for Qualification model.
 */
class ScheduleController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['staffmember'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Qualification models.
     * @return mixed
     */
    public function actionIndex()
    {  
        $teachersWithClass = (new \yii\db\Query())
            ->select(['distinct(ul.user_id) as id', 'concat(up.firstname,\' \',up.lastname) as name'])
            ->from('teacher_availability_day ta')
            ->join('Join', 'user_location ul', 'ul.id = ta.teacher_location_id')
            ->join('Join', 'user_profile up', 'up.user_id = ul.user_id')
            ->join('Join', 'qualification q', 'q.teacher_id = up.user_id')  
            ->join('Join', 'enrolment e', 'e.program_id = q.program_id')
            ->join('Join', 'lesson l', 'l.teacher_id = up.user_id')
            ->where('ul.location_id = :location_id', [':location_id'=>Yii::$app->session->get('location_id')])
            ->orderBy('id desc')
            ->all();

		$groupCourseTeachersWithClass = (new \yii\db\Query())
            ->select(['distinct(ul.user_id) as id', 'concat(up.firstname,\' \',up.lastname) as name'])
            ->from('teacher_availability_day ta')
            ->join('Join', 'user_location ul', 'ul.id = ta.teacher_location_id')
            ->join('Join', 'user_profile up', 'up.user_id = ul.user_id')
            ->join('Join', 'group_lesson gl', 'gl.teacher_id = up.user_id')
            ->where('ul.location_id = :location_id', [':location_id'=>Yii::$app->session->get('location_id')])
            ->orderBy('id desc')
            ->all();
           
		$teachersWithClass = array_unique(array_merge($teachersWithClass,$groupCourseTeachersWithClass),SORT_REGULAR);
			$allTeachers = (new \yii\db\Query())
            ->select(['distinct(ul.user_id) as id', 'concat(up.firstname,\' \',up.lastname) as name'])
            ->from('teacher_availability_day ta')
            ->join('Join', 'user_location ul', 'ul.id = ta.teacher_location_id')
            ->join('Join', 'user_profile up', 'up.user_id = ul.user_id')
            ->where('ul.location_id = :location_id', [':location_id'=>Yii::$app->session->get('location_id')])
            ->orderBy('id desc')
            ->all();
        
        $events = array();
        $events = (new \yii\db\Query())
            ->select(['l.teacher_id as resources', 'l.id as id', 'concat(s.first_name,\' \',s.last_name,\' (\',p.name,\' )\') as title, e.day, l.date as start, ADDTIME(l.date, e.duration) as end'])
            ->from('lesson l')
            ->join('Join', 'enrolment e', 'e.id = l.enrolment_id')
            ->join('Join', 'student s', 's.id = e.student_id')
            ->join('Join', 'program p', 'p.id = e.program_id')
            ->where('e.location_id = :location_id', [':location_id'=>Yii::$app->session->get('location_id')])
            ->all();
		foreach ($events as &$event) {
		$start = new \DateTime($event['start']);	
		$event['start'] = $start->format('Y-m-d H:i:s');	
		$end = new \DateTime($event['end']);	
		$event['end'] = $end->format('Y-m-d H:i:s');
		}
		unset($event);
		$groupLessonevents = (new \yii\db\Query())
            ->select(['gl.teacher_id as resources', 'gl.id as id', 'p.name as title, gc.day, gl.date as start, ADDTIME(gl.date, gc.length) as end'])
            ->from('group_lesson gl')
            ->join('Join', 'group_course gc', 'gc.id = gl.course_id')
            ->join('Join', 'program p', 'p.id = gc.program_id')
            ->where('gc.location_id = :location_id', [':location_id'=>Yii::$app->session->get('location_id')])
            ->all();

		$events = array_merge($events,$groupLessonevents);
		
        $location = Location::findOne($id=Yii::$app->session->get('location_id'));
		
		$location->from_time = new \DateTime($location->from_time);	
        $fromTime = $location->from_time;
		//$fromTime->setTimezone(new \DateTimeZone('US/Eastern'));	
        $from_time = $fromTime->format('H:i:s');
		
		$location->to_time = new \DateTime($location->to_time);	
        $toTime = $location->to_time;
		//$toTime->setTimezone(new \DateTimeZone('US/Eastern'));	
        $to_time = $toTime->format('H:i:s');
        
		return $this->render('index', ['teachersWithClass'=>$teachersWithClass, 'allTeachers'=>$allTeachers, 'events'=>$events, 'from_time'=>$from_time, 'to_time'=>$to_time]);
    }
    
    public function actionUpdateEvents(){
		$data = Yii::$app->request->rawBody;
		$data = Json::decode($data, true);
		$lesson = Lesson::findOne(['id' => $data['id']]);
		$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $lesson->date);
        $rescheduledLessonDate = clone $lessonDate;
		if((float)$data['minutes'] > 0) {
			$rescheduledLessonDate->add(new \DateInterval('PT' .round($data['minutes']).  'M'));	
		} else {
			$rescheduledLessonDate->sub(new \DateInterval('PT' . round(abs($data['minutes'])) . 'M'));	
		}
        $lesson->date = $rescheduledLessonDate->format('Y-m-d H:i:s');
		$lesson->save();
        
    }
    
}