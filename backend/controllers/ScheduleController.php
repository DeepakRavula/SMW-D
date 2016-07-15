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
            ->select(['q.teacher_id as resources', 'l.id as id', 'concat(s.first_name,\' \',s.last_name,\' (\',p.name,\' )\') as title, e.day, l.date as start, ADDTIME(l.date, e.duration) as end'])
            ->from('lesson l')
            ->join('Join', 'enrolment e', 'e.id = l.enrolment_id')
            ->join('Join', 'qualification q', 'q.teacher_id = l.teacher_id')            
            ->join('Join', 'student s', 's.id = e.student_id')
            ->join('Join', 'program p', 'p.id = q.program_id')
            ->where('e.location_id = :location_id', [':location_id'=>Yii::$app->session->get('location_id')])
            ->all();

        $location = Location::findOne($id=Yii::$app->session->get('location_id'));
        $from_time = $location->from_time;
        $to_time = $location->to_time;
        
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
        $program = $lesson->enrolment->program->name;
        $to_name[] = $lesson->teacherProfile->firstname;
        $to_mail[] = $lesson->teacher->email;
        $to_name[] = $lesson->enrolment->student->customerProfile->firstname;
        $to_mail[] = $lesson->enrolment->student->customer->email;
        for($i=0;$i<count($to_name);$i++)
        {
            Yii::$app->mailer->compose() 
                ->setFrom('smw@arcadiamusicacademy.com')   
                ->setTo($to_mail[$i]) 
                ->setSubject('Re-Schedule Notification') 
                ->setHtmlBody('Dear ' . $to_name[$i] . ',<br>
                    <br>
                    Your ' . $program . ' lesson has been Re-scheduled from '. $lessonDate->format('d-m-Y H:i') .' to ' . $rescheduledLessonDate->format('d-m-Y H:i') . '.<br>
                    <br>
                    Thank you<br>
                    Arcadia Music Academy Team.<br>')
                ->send();
        }
    }
    
}