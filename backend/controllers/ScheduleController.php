<?php

namespace backend\controllers;

use Yii;
use common\models\Qualification;
use common\models\TeacherAvailability;
use backend\models\search\QualificationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

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
            ->select(['ta.teacher_id as id', 'concat(up.firstname,\' \',up.lastname) as name'])
            ->from('teacher_availability_day ta')
            ->join('Join', 'user_profile up', 'up.user_id = ta.teacher_id')
            ->where('ta.location_id = :location_id AND ta.day = DATE_FORMAT(now(),\'%w\')', [':location_id'=>1])
            ->all();
        
        $events = array();
        $student = array();
        
        foreach($teacherAvailability as $teacher){
            
            $studentAvailability = (new \yii\db\Query())
                ->select(['q.teacher_id as id', 'concat(s.first_name,\' \',s.last_name) as name, ed.from_time, ed.to_time'])
                ->from('enrolment e')
                ->join('Join', 'qualification q', 'q.id = e.qualification_id')
                ->join('Join', 'enrolment_schedule_day ed', 'ed.enrolment_id = e.id')
                ->join('Join', 'student s', 's.id = e.student_id')
                ->where('q.teacher_id = :teacher_id AND ed.day = DATE_FORMAT(now(),\'%w\')', [':teacher_id'=>$teacher['id']])
                ->all();
                
            foreach($studentAvailability as $studentAvailability){
                $student['title'] = $studentAvailability['name'];
                $student['start'] = date("D M d Y").' '.$studentAvailability["from_time"];
                $student['end'] = date("D M d Y").' '.$studentAvailability["to_time"];
                $student['allDay'] = false;
                $student['resources'] = $studentAvailability['id']; 
                
                array_push($events, $student);
            } 
            
        }
        
		return $this->render('index', ['teacherAvailability'=>$teacherAvailability, 'events'=>$events]);
    }

}