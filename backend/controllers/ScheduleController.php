<?php

namespace backend\controllers;

use Yii;
use common\models\Qualification;
use common\models\TeacherAvailability;
use common\models\Location;
use common\models\Lesson;
use backend\models\search\QualificationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
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
        $events = (new \yii\db\Query())
            ->select(['q.teacher_id as resources', 'l.id as id', 'concat(s.first_name,\' \',s.last_name,\' (\',p.name,\' )\') as title, ed.day, l.date as start, concat(DATE(l.date),\' \',ed.to_time) as end'])
            ->from('lesson l')
            ->join('Join', 'enrolment_schedule_day ed', 'ed.id = l.enrolment_schedule_day_id')
            ->join('Join', 'enrolment e', 'e.id = ed.enrolment_id')
            ->join('Join', 'qualification q', 'q.id = e.qualification_id')            
            ->join('Join', 'student s', 's.id = e.student_id')
            ->join('Join', 'program p', 'p.id = q.program_id')
            ->where('e.location_id = :location_id', [':location_id'=>Yii::$app->session->get('location_id')])
            ->all();

        $location = Location::findOne($id=Yii::$app->session->get('location_id'));
        $from_time = $location->from_time;
        $to_time = $location->to_time;
        
		return $this->render('index', ['teacherAvailability'=>$teacherAvailability, 'events'=>$events, 'from_time'=>$from_time, 'to_time'=>$to_time]);
    }
    
    public function actionUpdateEvents(){
		$data = Yii::$app->request->rawBody;
		$data = Json::decode($data, true);
		
		$day = $data['minutes'] / (24*60);
		$lessonDate = Lesson::findOne(['id' => $data['id']]);
		$date = new \DateTime($lessonDate->date);
		$date->modify('+' .$day. 'day');
		$lessonModel = Lesson::findOne(['id' => $data['id']]);
		$lessonModel->date = $date->format('Y-m-d'); 
		$lessonModel->update(); 
    }
}