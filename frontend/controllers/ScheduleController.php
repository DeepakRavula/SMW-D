<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use common\models\Lesson;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Program;
use common\models\User;
use common\models\TeacherAvailability;
use common\models\LocationAvailability;
use common\models\UserLocation;
use frontend\models\search\LocationScheduleSearch;
use backend\models\search\ScheduleSearch;
use common\models\Holiday;
use common\models\TeacherUnavailability;
use Carbon\Carbon;
use common\components\controllers\FrontendBaseController;
use Carbon\CarbonInterval;
use League\Period\Period;
use yii\helpers\Url;
use common\models\Location;
use common\models\Enrolment;
use common\models\Student;
use common\models\LessonPayment;
use common\models\Note;
use yii\data\ActiveDataProvider;
use common\models\log\LogHistory;
use yii\web\NotFoundHttpException;


/**
 * QualificationController implements the CRUD actions for Qualification model.
 */
class ScheduleController extends FrontendBaseController
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
                        'actions' => ['index', 'view'],
                        'roles' => ['staffmember'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return $this->redirect('/user/sign-in/login');
                }
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['render-day-events', 'render-classroom-events', 'fetch-holiday-name',
                   'render-resources', 'render-classroom-resources'],
                'formatParam' => '_format',
                'formats' => [
                   'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Lists all Qualification models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $userLocation = UserLocation::findOne(['user_id' => $userId]);
        $locationId = $userLocation->location_id;
        $searchModel = new ScheduleSearch();
        $searchModel->goToDate = Yii::$app->formatter->asDate(new \DateTime());
        $date = new \DateTime();
        $scheduleVisibilities = LocationAvailability::find()
            ->notDeleted()
            ->location($locationId)
            ->scheduleVisibilityHours()
            ->all();
        $scheduleVisibility = LocationAvailability::find()
            ->notDeleted()
            ->location($locationId)
            ->day($date->format('N'))
            ->scheduleVisibilityHours()
            ->one();
        $locationAvailabilities = LocationAvailability::find()
            ->notDeleted()
            ->location($locationId)
            ->locationaAvailabilityHours()
            ->all();
        $locationAvailability = LocationAvailability::find()
            ->notDeleted()
            ->location($locationId)
            ->day($date->format('N'))
            ->locationaAvailabilityHours()
            ->one();
	$holiday = Holiday::find()->andWhere(['DATE(`date`)' => $date->format('Y-m-d')])->one();
        $holidayResource = '';
        if (!empty($holiday)) {
            $holidayResource = ' (' . $holiday->description . ')';
        }
        return $this->render('index', [
            'locationAvailabilities'   => $locationAvailabilities,
            'scheduleVisibilities'     => $scheduleVisibilities,
            'locationId'               => $locationId,
            'searchModel' => $searchModel,
            'name' => $holidayResource
        ]);
    }

    public function actionFetchHolidayName($date)
    {
        $holiday = Holiday::findOne(['DATE(date)' => $date]);
        $holidayResource = '';
        if (!empty($holiday)) {
            $holidayResource = ' (' . $holiday->description . ')';
        }
        $titile = 'Schedule for ' . (new \DateTime($date))->format('l, F jS, Y') . ' ' . $holidayResource;
        return $titile;
    }

    public function getLessons($userId, $date)
    {
        $user = User::findOne(['id' => $userId]);
        $roles = Yii::$app->authManager->getRolesByUser($userId);
        $role = end($roles);
        if ($role->name === User::ROLE_CUSTOMER) {
            $studentIds = ArrayHelper::getColumn($user->student, 'id');
        }
        $userLocation = UserLocation::findOne(['user_id' => $userId]);
        $locationId = $userLocation->location_id;
        $query = Lesson::find()
            ->isConfirmed()
            ->joinWith(['course' => function ($query) use ($locationId) {
                $query->andWhere(['course.locationId' => $locationId])
                        ->confirmed()
                        ->notDeleted();
            }]);
        if (!empty($studentIds)) {
            $query->joinWith(['enrolment' => function ($query) use ($studentIds) {
                $query->joinWith(['student' => function ($query) use ($studentIds) {
                    $query->andWhere(['student.id' => $studentIds]);
                }]);
            }]);
        }
        if ($role->name === User::ROLE_TEACHER) {
            $query->andWhere(['lesson.teacherId' => $userId]);
        }
        if ($role->name === User::ROLE_CUSTOMER) {
            $query->present();
        }
        $query->scheduledOrRescheduled()
            ->andWhere(['DATE(lesson.date)' => $date->format('Y-m-d')])
            ->notDeleted();
        $lessons = $query->all();
        return $lessons;
    }

    public function getTeacherAvailability($teacherId, $date)
    {
        $formatedDate = $date->format('Y-m-d');
        $availabilities = TeacherAvailability::find()
            ->andWhere(['day' => $date->format('N')])
            ->joinWith(['userLocation ul' => function ($query) use ($teacherId) {
                $query->andWhere(['ul.user_id' => $teacherId]);
            }])
			->notDeleted()
            ->all();
        return $availabilities;
    }

    public function getTeacherUnavailability($teacherAvailability, $date)
    {
        $availability = TeacherAvailability::findOne($teacherAvailability->id);
        $unavailability = TeacherUnavailability::find()
            ->andWhere(['teacherId' => $availability->teacher->id])
            ->overlap($date)
            ->all();
        return $unavailability;
    }

    public function getTeacherAvailabilityEvents($teachersAvailabilities, $date)
    {
        $events = [];
        foreach ($teachersAvailabilities as $teachersAvailability) {
            $unavailabilities = $this->getTeacherUnavailability($teachersAvailability, $date);
                if (!empty($unavailabilities)) {
                    foreach ($unavailabilities as $unavailability) {
                if (empty($unavailability->fromTime) && empty($unavailability->toTime) || $unavailability->fromTime === 
                    $teachersAvailability->from_time && $unavailability->toTime === $teachersAvailability->to_time) {
                    continue;
                } else {
                    $events = array_merge($events, $this->getAvailabilityEvents($teachersAvailability, $unavailability, $date));
                }
            }
         } else {
                $events[] = $this->getRegularAvailability($teachersAvailability, $date);
            }
        }
        return $events;
    }

    public function getAvailabilityEvents($teachersAvailability, $unavailability, $date)
    {
        $events = [];
        $availabilityStartDateTime = new \DateTime($teachersAvailability->from_time);
        $availabilityEndDateTime = new \DateTime($teachersAvailability->to_time);
        $availabilityStart = clone $date->setTime($availabilityStartDateTime->format('H'), $availabilityStartDateTime->format('i'), 
            $availabilityStartDateTime->format('s'));
        $availabilityEnd = clone $date->setTime($availabilityEndDateTime->format('H'), $availabilityEndDateTime->format('i'), 
            $availabilityEndDateTime->format('s'));

        $unavailabilityStartDateTime = new \DateTime($unavailability->fromTime);
        $unavailabilityEndDateTime = new \DateTime($unavailability->toTime);
        $unavailabilityStart = clone $date->setTime($unavailabilityStartDateTime->format('H'), $unavailabilityStartDateTime->format('i'), 
            $unavailabilityStartDateTime->format('s'));
        $unavailabilityEnd = clone $date->setTime($unavailabilityEndDateTime->format('H'), $unavailabilityEndDateTime->format('i'), 
            $unavailabilityEndDateTime->format('s'));
            
        if ($unavailabilityStart > $availabilityStart && $availabilityEnd > $unavailabilityEnd) {
            $start = $availabilityStart;
            $end = $unavailabilityStart;
            $events[] = $this->setEvents($teachersAvailability, $start, $end);
            $start = $unavailabilityEnd;
            $end = $availabilityEnd;
            $events[] = $this->setEvents($teachersAvailability, $start, $end);
        } elseif ($unavailabilityStart < $availabilityStart && $availabilityEnd > $unavailabilityEnd) {
            $start = $unavailabilityEnd;
            $end = $availabilityEnd;
            $events[] = $this->setEvents($teachersAvailability, $start, $end);
        } elseif ($unavailabilityStart > $availabilityStart && $availabilityEnd < $unavailabilityEnd) {
            $start = $availabilityStart;
            $end = $unavailabilityStart;
            $events[] = $this->setEvents($teachersAvailability, $start, $end);
        }
        return $events;
    }

    public function setEvents($teachersAvailability, $start, $end)
    {
        return [
            'resourceId' => $teachersAvailability->teacher->id,
            'title'      => '',
            'start'      => $start->format('Y-m-d H:i:s'),
            'end'        => $end->format('Y-m-d H:i:s'),
            'rendering'  => 'background',
        ];
    }

    public function actionRenderDayEvents()
    {
        $scheduleRequest = Yii::$app->request->get('ScheduleSearch');
        $userId = $scheduleRequest['userId'];
        $showAll = $scheduleRequest['showAll'];
        $date = new \DateTime($scheduleRequest['date']);
        $teachersAvailabilities = $this->getTeacherAvailability($userId, $date);
        $events = $this->getTeacherAvailabilityEvents($teachersAvailabilities, $date);
        $lessons = $this->getLessons($userId, $date);
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->duration);
            $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
            if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                $description = $this->renderAjax('group-lesson-description', [
                    'lesson' => $lesson,
                    'view' => Lesson::TEACHER_VIEW
                ]);
                $title = $lesson->course->program->name;
                $class = 'group-lesson';
                $backgroundColor = null;
                if (!empty($lesson->colorCode)) {
                    $class = null;
                    $backgroundColor = $lesson->colorCode;
                }
            } else {
                $title = $lesson->enrolment->student->fullName;
                $class = $lesson->class;
                $backgroundColor = $lesson->getColorCode();
                $description = $this->renderAjax('private-lesson-description', [
                    'title' => $title,
                    'lesson' => $lesson,
                    'view' => Lesson::TEACHER_VIEW
                ]);
            }

            $events[] = [
                'lessonId' => $lesson->id,
                'resourceId' => $lesson->teacherId,
                'title' => $title,
                'start' => $lesson->date,
                'end' => $toTime->format('Y-m-d H:i:s'),
                'className' => $class,
                'backgroundColor' => $backgroundColor,
                'description' => $description,
                'icon' => $lesson->is_online == 1 ? 'laptop' : '',
                'url' => Url::to(['/schedule/view?id=' . $lesson->id])
            ];
        }
        unset($lesson);
        return $events;
    }

    public function getRegularAvailability($teachersAvailability, $date)
    {
        $startTime = Carbon::parse($teachersAvailability->from_time);
        $start = $date->setTime($startTime->hour, $startTime->minute, $startTime->second);
        $endTime = Carbon::parse($teachersAvailability->to_time);
        $end = clone $date;
        $end = $end->setTime($endTime->hour, $endTime->minute, $endTime->second);
        $availability = TeacherAvailability::findOne($teachersAvailability->id);
        return [
            'resourceId' => $availability->teacher->id,
            'title'      => '',
            'start'      => $start->format('Y-m-d H:i:s'),
            'end'        => $end->format('Y-m-d H:i:s'),
            'rendering'  => 'background',
        ];
    }

    public function actionView($id) {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = $this->findModel($id);
        $enrolment = Enrolment::findOne(['courseId' => $model->courseId]);
        $notes = Note::find()
            ->andWhere(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_LESSON])
            ->orderBy(['createdOn' => SORT_DESC]);

        $noteDataProvider = new ActiveDataProvider([
            'query' => $notes,
        ]);

        $groupLessonStudents = Student::find()
            ->notDeleted()
            ->joinWith(['enrolments' => function ($query) use ($id) {
                $query->joinWith(['course' => function ($query) use ($id) {
                    $query->joinWith(['program' => function ($query) use ($id) {
                        $query->group();
                    }]);
                    $query->joinWith(['lessons' => function ($query) use ($id) {
                        $query->andWhere(['lesson.id' => $id]);
                    }])
                        ->confirmed()
                        ->notDeleted();
                }])
                    ->notDeleted()
                    ->isConfirmed();
            }])
            ->location($locationId);

        $studentDataProvider = new ActiveDataProvider([
            'query' => $groupLessonStudents,
        ]);
        $payments = LessonPayment::find()
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted();
            }])
            ->andWhere(['lesson_payment.lessonId' => $id])
            ->notDeleted();
        $paymentsDataProvider = new ActiveDataProvider([
            'query' => $payments
        ]);
        $logDataProvider = new ActiveDataProvider([
            'query' => LogHistory::find()->lesson($id)
        ]);

        return $this->render('view', [
            'model' => $model,
            'noteDataProvider' => $noteDataProvider,
            'studentDataProvider' => $studentDataProvider,
            'paymentsDataProvider' => $paymentsDataProvider,
            'logDataProvider' => $logDataProvider,
        ]);
    }

    protected function findModel($id)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = Lesson::find()->location($locationId)
            ->notDeleted()
            ->andWhere(['lesson.id' => $id])->one();
        if ($model !== null) {
            if ($model->leaf) {
                if (!$model->leaf->isCanceled()) {
                    $this->redirect(['lesson/view', 'id' => $model->leaf->id]);
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            } else if ($model->isCanceled()) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
