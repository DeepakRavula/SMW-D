<?php

namespace backend\controllers;

use Yii;
use common\models\Location;
use common\models\Lesson;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\filters\AccessControl;
use common\models\Program;
use common\models\Course;
use yii\helpers\Url;
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
            ->join('Join', 'lesson l', 'l.teacherId = up.user_id')
            ->where('ul.location_id = :location_id', [':location_id'=>Yii::$app->session->get('location_id')])
            ->andWhere(['not', ['l.status'  =>  [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
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
        
        $lessons =[];
        $lessons = Lesson::find()
            ->joinWith(['enrolment' => function($query) {
				$query->joinWith(['course' => function($query) {
				    $query->andWhere(['locationId' => Yii::$app->session->get('location_id')]);
                    $query->joinWith(['program']);
			    }])
                ->joinWith(['student']);
			}])
            ->andWhere(['not', ['lesson.status'  =>  [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
            ->all();                
        
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->course->duration);
		    $toTime->add(new \DateInterval('PT' . $length[0] . 'H' . $length[1] . 'M'));
            $title = $lesson->enrolment->student->fullName . ' ( ' .$lesson->course->program->name . ' ) ';
            if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {                
                $title = $lesson->course->program->name; 
            }
            $events[]= [
                'resources' => $lesson->teacherId,
                'title' => $title,
                'start' => $lesson->date,
                'end' => $toTime->format('Y-m-d H:i:s'),
                'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
            ];
           
        }
        unset($lesson);
        
        $location = Location::findOne($id=Yii::$app->session->get('location_id'));
		
		$location->from_time = new \DateTime($location->from_time);	
        $fromTime = $location->from_time;
        $from_time = $fromTime->format('H:i:s');
		
		$location->to_time = new \DateTime($location->to_time);	
        $toTime = $location->to_time;
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