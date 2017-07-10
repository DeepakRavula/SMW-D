<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\UserLocation;
use common\models\TeacherAvailability;
use backend\models\TeacherAvailabilitySearch;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\Lesson;
use common\models\Program;
use common\models\Invoice;
use yii\filters\ContentNegotiator;
use common\models\TeacherRoom;
use yii\bootstrap\ActiveForm;
use common\models\Location;
use common\models\TeacherAvailabilityLog;
/**
 * TeacherAvailabilityController implements the CRUD actions for TeacherAvailability model.
 */
class TeacherAvailabilityController extends Controller
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
			'contentNegotiator' => [
				'class' => ContentNegotiator::className(),
				'only' => ['modify', 'delete', 'events'],
				'formatParam' => '_format',
				'formats' => [
				   'application/json' => Response::FORMAT_JSON,
				],
			],	 
        ];
    }

    /**
     * Lists all TeacherAvailability models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TeacherAvailabilitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TeacherAvailability model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TeacherAvailability model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TeacherAvailability();
        $model->location_id = Yii::$app->session->get('location_id');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing TeacherAvailability model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing TeacherAvailability model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
   
    public function actionDelete($id)
    {
        $status=false;
        $availabilityModel = $this->findModel($id);
        $user = User::findOne(['id' => Yii::$app->user->id]);
        $availabilityModel->userName = $user->publicIdentity;
        $availabilityModel->on(TeacherAvailability::EVENT_DELETE, [new TeacherAvailabilityLog(), 'deleteAvailability']);

        if ($availabilityModel->delete()) {
            $status=true;
            $availabilityModel->trigger(TeacherAvailability::EVENT_DELETE);
        }

        return [
            'status' => $status
        ];
    }
    /**
     * Finds the TeacherAvailability model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return TeacherAvailability the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TeacherAvailability::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAvailabilityWithEvents($id)
    {
        $session = Yii::$app->session;
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $locationId = $session->get('location_id');
        $teacherAvailabilities = TeacherAvailability::find()
        ->joinWith(['userLocation' => function ($query) use ($id) {
            $query->joinWith(['userProfile' => function ($query) use ($id) {
                $query->where(['user_profile.user_id' => $id]);
            }]);
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
            ->joinWith(['course' => function ($query) {
                $query->andWhere(['locationId' => Yii::$app->session->get('location_id')]);
            }])
            ->where(['lesson.teacherId' => $id])
        	->andWhere(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED]])
			->notDeleted()
            ->all();
        $events = [];
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->fullDuration);
            $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
            if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                $title = $lesson->course->program->name.' ( '.$lesson->course->getEnrolmentsCount().' ) ';
            } else {
                $title = $lesson->enrolment->student->fullName.' ( '.$lesson->course->program->name.' ) ';
            }
            $class = $lesson->class;
            $events[] = [
                'start' => $lesson->date,
                'end' => $toTime->format('Y-m-d H:i:s'),
                'className' => $class,
                'title' => $title,
            ];
        }
        unset($lesson);

        return [
            'availableHours' => $availableHours,
            'events' => $events,
        ];
    }
	public function actionModify($id, $teacherId)
    {
        $teacherModel = User::findOne($teacherId);
        $teacherAvailabilityModel = TeacherAvailability::findOne($id);
        if (empty ($teacherAvailabilityModel)) {
            $teacherAvailabilityModel = new TeacherAvailability();
            $userModel = User::findOne(['id' => Yii::$app->user->id]);
            $teacherAvailabilityModel->userName = $userModel->publicIdentity;
            $teacherAvailabilityModel->on(TeacherAvailability::EVENT_CREATE, [new TeacherAvailabilityLog(), 'create']);
            $teacherAvailabilityModel->teacher_location_id = $teacherModel->userLocation->id;
            $roomModel = new TeacherRoom();
        } else if (empty ($teacherAvailabilityModel->teacherRoom)) {
            $roomModel = new TeacherRoom();
        } else {
            $roomModel = $teacherAvailabilityModel->teacherRoom;
        }
        if (!empty($teacherAvailabilityModel)) {
            $userModel = User::findOne(['id' => Yii::$app->user->id]);
            $teacherAvailabilityModel->userName = $userModel->publicIdentity;
            $teacherAvailabilityModel->on(TeacherAvailability::EVENT_UPDATE, [new TeacherAvailabilityLog(), 'edit'], ['oldAttributes' => $teacherAvailabilityModel->getOldAttributes()]);
            $roomModel->availabilityId = $teacherAvailabilityModel->id;
        }
        $roomModel->teacher_location_id = $teacherModel->userLocation->id;
        $fromTime         = new \DateTime($teacherAvailabilityModel->from_time);
        $toTime           = new \DateTime($teacherAvailabilityModel->to_time);
        $roomModel->from_time = $fromTime->format('g:i A');
        $roomModel->to_time   = $toTime->format('g:i A');
        $post             = Yii::$app->request->post();
        $roomModel->setScenario(TeacherRoom::SCENARIO_AVAILABIITY_EDIT);
        $roomModel->day = $teacherAvailabilityModel->day;
        $data =  $this->renderAjax('/user/teacher/_form-teacher-availability', [
            'model' => $teacherModel,
            'roomModel' => $roomModel,
            'teacherAvailabilityModel' => $teacherAvailabilityModel,
        ]);
        if ($roomModel->load($post)) {
            $fromTime         = new \DateTime($roomModel->from_time);
            $toTime           = new \DateTime($roomModel->to_time);
            $teacherAvailabilityModel->from_time = $fromTime->format('H:i:s');
            $teacherAvailabilityModel->to_time   = $toTime->format('H:i:s');
            $teacherAvailabilityModel->day = $roomModel->day;
            $roomModel->from_time = $fromTime->format('H:i:s');
            $roomModelToTime = $toTime->modify('-1 second');
            $roomModel->to_time = $roomModelToTime->format('H:i:s');
            if ($roomModel->validate()) {
                $teacherAvailabilityModel->save();
                if (!empty($roomModel->classroomId)) {
                    $roomModel->availabilityId = $teacherAvailabilityModel->id;
                    $roomModel->teacherAvailabilityId = $teacherAvailabilityModel->id;
                    $roomModel->save();
                } else {
                    TeacherRoom::deleteAll(['teacherAvailabilityId' => $teacherAvailabilityModel->id]);
                }

                return  [
                    'status' => true,
                ];
            } else {
                $errors = ActiveForm::validate($roomModel);
                return [
                    'status' => false,
                    'errors' => $errors,
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }
	
    public function actionEvents($id)
    {
        $session    = Yii::$app->session;
        $locationId = $session->get('location_id');
        $location   = Location::findOne($locationId);
        $events     = [];
        foreach ($location->locationAvailabilities as $availability) {
            $startTime = new \DateTime($availability->fromTime);
            $endTime   = new \DateTime($availability->toTime);
            $events[]  = [
                'resourceId' => $availability->day,
                'start'      => $startTime->format('Y-m-d H:i:s'),
                'end'        => $endTime->format('Y-m-d H:i:s'),
                'rendering'  => 'background',
                'backgroundColor' => '#ffffff',
            ];
        }
        $teacherAvailabilities = TeacherAvailability::find()
                ->joinWith('userLocation')
                ->where(['user_id' => $id])
                ->all();
        foreach ($teacherAvailabilities as $teacherAvailability) {
            $title = null;
            if (!empty($teacherAvailability->teacherRoom->classroom->name)) {
                $title = $teacherAvailability->teacherRoom->classroom->name;
            }
            $startTime = new \DateTime($teacherAvailability->from_time);
            $endTime   = new \DateTime($teacherAvailability->to_time);
            $events[]  = [
                'title'      => $title,
                'id'         => $teacherAvailability->id,
                'resourceId' => $teacherAvailability->day,
                'start'      => $startTime->format('Y-m-d H:i:s'),
                'end'        => $endTime->format('Y-m-d H:i:s'),
                'backgroundColor' => '#97ef83',
            ];
        }
        return $events;
    }
}
