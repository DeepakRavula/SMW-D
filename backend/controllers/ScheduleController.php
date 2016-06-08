<?php

namespace backend\controllers;

use Yii;
use common\models\Qualification;
use common\models\TeacherAvailability;
use common\models\Location;
use backend\models\search\QualificationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

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
        
        /* $teacherAvailability = ArrayHelper::map(TeacherAvailability::find()->all(), 'id', 'teacher_id as name');*/
        $teacherAvailability = (new \yii\db\Query())
            ->select(['distinct(ul.user_id) as id', 'concat(up.firstname,\' \',up.lastname) as name'])
            ->from('teacher_availability_day ta')
            ->join('Join', 'user_location ul', 'ul.id = ta.teacher_location_id')
            ->join('Join', 'user_profile up', 'up.user_id = ul.user_id')
            ->where('ul.location_id = :location_id', [':location_id'=>Yii::$app->session->get('location_id')])
            ->orderBy('id desc')
            ->all();
        
        $events = array();
        $student = array();
        $students = array();
        
        foreach($teacherAvailability as $teacher){
            
            $studentEvents = (new \yii\db\Query())
                ->select(['q.teacher_id as id', 'concat(s.first_name,\' \',s.last_name,\' (\',p.name,\' )\') as title, HOUR(ed.from_time) as start_hours, MINUTE(ed.from_time) as start_minutes, HOUR(ed.to_time) as end_hours, MINUTE(ed.to_time) as end_minutes, ed.day, e.commencement_date, e.renewal_date'])
                ->from('enrolment e')
                ->join('Join', 'qualification q', 'q.id = e.qualification_id')
                ->join('Join', 'enrolment_schedule_day ed', 'ed.enrolment_id = e.id')
                ->join('Join', 'student s', 's.id = e.student_id')
                ->join('Join', 'program p', 'p.id = q.program_id')
                ->where('q.teacher_id = :teacher_id', [':teacher_id'=>$teacher['id']])
                ->all();
                
            foreach($studentEvents as $studentEvents){
                $student['title'] = $studentEvents['title'];
                $student['start_hours'] = $studentEvents["start_hours"];
                $student['end_hours'] = $studentEvents["end_hours"];
                $student['start_minutes'] = $studentEvents["start_minutes"];
                $student['end_minutes'] = $studentEvents["end_minutes"];
                $student['allDay'] = false;
                $student['resources'] = $studentEvents['id']; 
                $student['day'] = $studentEvents['day'];
                $student['commencement_date'] = $studentEvents['commencement_date']; 
                $student['renewal_date'] = $studentEvents['renewal_date'];
                
                array_push($events, $student);
            } 
            
        }
        
        $location = Location::findOne($id=Yii::$app->session->get('location_id'));
        $from_time = $location->from_time;
        $to_time = $location->to_time;
        
		return $this->render('index', ['teacherAvailability'=>$teacherAvailability, 'events'=>$events, 'student'=>$events, 'from_time'=>$from_time, 'to_time'=>$to_time]);
    }

}