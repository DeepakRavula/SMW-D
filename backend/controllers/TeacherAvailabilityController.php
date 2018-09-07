<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\TeacherAvailability;
use backend\models\TeacherAvailabilitySearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\Lesson;
use common\models\Program;
use yii\filters\ContentNegotiator;
use common\models\TeacherRoom;
use yii\bootstrap\ActiveForm;
use common\models\Location;
use common\models\LocationAvailability;
use common\components\controllers\BaseController;
use yii\filters\AccessControl;
use yii\helpers\Url;

/**
 * TeacherAvailabilityController implements the CRUD actions for TeacherAvailability model.
 */
class TeacherAvailabilityController extends BaseController
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
                'only' => ['modify', 'delete', 'events', 'show-lesson-event',
                    'availability'
                ],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'view', 'delete', 'create', 
                            'modify', 'events', 'show-lesson-event', 'availability'],
                        'roles' => ['manageTeachers'],
                    ],
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
        $model->location_id = Location::findOne(['slug' => \Yii::$app->location])->id;

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
      
        $availabilityModel->delete();
        $response = [
            'status' => true,
            'url' => Url::to(['user/view', 'id' => $availabilityModel->teacher->id]),
        ];
        return $response;
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

    public function actionAvailability($id = null)
    {
        $scheduleRequest = Yii::$app->request->get('ScheduleSearch');
        $locationVisibility = $scheduleRequest['locationVisibility'];
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $teacherAvailabilities = [];
        if ($id) {
            $teacherAvailabilities = TeacherAvailability::find()
                ->joinWith(['userLocation' => function ($query) use ($id) {
                    $query->joinWith(['userProfile' => function ($query) use ($id) {
                        $query->andWhere(['user_profile.user_id' => $id]);
                    }]);
                }])
                ->notDeleted()
                ->all();
        }
        $data =  $this->renderAjax('/layouts/datepicker', ['isShowAllChecked' => $locationVisibility]);
        $availableHours = [];
        foreach ($teacherAvailabilities as $teacherAvailability) {
            $availableHours[] = [
                'start' => $teacherAvailability->from_time,
                'end' => $teacherAvailability->to_time,
                'dow' => [$teacherAvailability->day],
                'className' => 'teacher-available',
                'rendering' => 'background',
            ];
        }
        $query = LocationAvailability::find()
            ->location($locationId);
        if (!$locationVisibility) {
            $query->scheduleVisibilityHours();
        } else {
            $query->locationaAvailabilityHours();
        }
        $minLocationAvailability = $query->orderBy(['fromTime' => SORT_ASC])
            ->one();
        $maxLocationAvailability = $query->orderBy(['toTime' => SORT_DESC])
            ->one();
        if (empty($minLocationAvailability)) {
            $minTime = LocationAvailability::DEFAULT_FROM_TIME;
        } else {
            $minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
        }
        if (empty($maxLocationAvailability)) {
            $maxTime = LocationAvailability::DEFAULT_TO_TIME;
        } else {
            $maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
        }
        
        return [
            'availableHours' => $availableHours,
            'minTime' => $minTime,
            'maxTime' => $maxTime,
            'data' => $data
        ];
    }
    public function actionModify($id, $teacherId)
    {
        $teacherModel = User::findOne($teacherId);
        $teacherAvailabilityModel = TeacherAvailability::findOne($id);
        if (empty($teacherAvailabilityModel)) {
            $teacherAvailabilityModel = new TeacherAvailability();
            $teacherAvailabilityModel->teacher_location_id = $teacherModel->userLocation->id;
            $roomModel = new TeacherRoom();
        } elseif (empty($teacherAvailabilityModel->teacherRoom)) {
            $roomModel = new TeacherRoom();
        } else {
            $roomModel = $teacherAvailabilityModel->teacherRoom;
        }
        if (!empty($teacherAvailabilityModel)) {
            $userModel = User::findOne(['id' => Yii::$app->user->id]);
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
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
                ->andWhere(['user_id' => $id])
                ->notDeleted()
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

    public function actionShowLessonEvent()
    {
        $lessonRequest = Yii::$app->request->get('LessonSearch');
        $teacherId = $lessonRequest['teacherId'];
        $studentId = $lessonRequest['studentId'];
        $lessonId = $lessonRequest['lessonId'];
        $enrolmentId = $lessonRequest['enrolmentId'];
        $date = $lessonRequest['date'];
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $fromDate = (new \DateTime($date))->modify('Monday this week');
        $toDate = (new \DateTime($date))->modify('Sunday this week');
        if ($teacherId) {
            $teacherLessons = Lesson::find()
                ->joinWith(['course' => function ($query) use ($locationId, $enrolmentId) {
                    $query->location($locationId)
                        ->confirmed()
                        ->andWhere(['NOT', ['course.id' => null]]);
                    if ($enrolmentId) {
                        $query->joinWith(['enrolments' => function ($query) use ($enrolmentId) {
                            $query->andWhere(['NOT', ['enrolment.id' => $enrolmentId]]);
                        }]);
                    }
                }])
                ->scheduledOrRescheduled()
                ->isConfirmed()
                ->present()
                ->notDeleted()
                ->between($fromDate, $toDate)
                ->andWhere(['lesson.teacherId' => $teacherId]);
        }
        $lessonQuery = Lesson::find()
            ->joinWith(['course' => function ($query) use ($studentId, $locationId, $enrolmentId) {
                $query->joinWith(['enrolments' => function ($query) use ($studentId, $enrolmentId) {
                    if ($studentId) {
                        $query->andWhere(['enrolment.studentId' => $studentId]);
                    }
                    if ($enrolmentId) {
                        $query->andWhere(['NOT', ['enrolment.id' => $enrolmentId]]);
                    }
                }]);
                $query->location($locationId)
                    ->confirmed()
                    ->andWhere(['NOT', ['course.id' => null]]);
            }])
            ->scheduledOrRescheduled()
            ->isConfirmed()
            ->present()
            ->notDeleted()
            ->andWhere(['NOT', ['lesson.id' => $lessonId]])
            ->between($fromDate, $toDate);
            if ($teacherId) {
                $lessonQuery->andWhere(['lesson.teacherId' => $teacherId]);
                $lessonQuery->union($teacherLessons);
            }
            $lessons = $lessonQuery->all();
        $events = [];
        foreach ($lessons as $lesson) {
            $lesson = Lesson::findOne($lesson->id);
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->duration);
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
        return $events;
    }
}
