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
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['render-day-events', 'render-classroom-events',
                   'render-resources', 'render-classroom-resources','render-calendar-time'],
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
        $date = new \DateTime();
        $locationAvailability = LocationAvailability::find()
		->andWhere(['locationId' => $locationId])
               ->andWhere(['day' => $date->format('N')])
	->andWhere(['type' => LocationAvailability::TYPE_OPERATION_TIME])
		->one();
        $minAvailability = LocationAvailability::find()
                ->andWhere(['locationId' => $locationId])
		->andWhere(['type' => LocationAvailability::TYPE_OPERATION_TIME])
                ->orderBy(['fromTime' => SORT_ASC])
                ->one();
        $maxAvailability = LocationAvailability::find()
                ->andWhere(['locationId' => $locationId])
		->andWhere(['type' => LocationAvailability::TYPE_OPERATION_TIME])
		->orderBy(['toTime' => SORT_DESC])
                ->one();
	
        $week_from_time = $minAvailability->fromTime;
        $week_to_time   = $maxAvailability->toTime;
        $from_time = $locationAvailability->fromTime;
        $to_time = $locationAvailability->toTime;
        return $this->render('index', [
            'from_time'                => $from_time,
            'to_time'                  => $to_time,
	    'week_from_time'	       => $week_from_time,
	    'week_to_time'	       => $week_to_time,
        ]);
    }

    public function getLessons($userId)
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
                        ->confirmed();
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
        $query->scheduledOrRescheduled()
                ->notDeleted();
        $lessons = $query->all();
        return $lessons;
    }

    public function actionRenderDayEvents($userId)
    {
        $teachersAvailabilities = TeacherAvailability::find()
            ->joinWith(['userLocation' => function ($query) use ($userId) {
                $query->where(['user_location.user_id' => $userId]);
            }])
            ->all();

        foreach ($teachersAvailabilities as $teachersAvailability) {
            $start = $teachersAvailability->from_time;
            $end   = $teachersAvailability->to_time;
            $events[] = [
                'resourceId' => $teachersAvailability->teacher->id,
                'title'      => '',
                'start'      => $start,
                'end'        => $end,
                'rendering'  => 'background',
            ];
        }
        $lessons = $this->getLessons($userId);
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->fullDuration);
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
                $class = 'private-lesson';
                $backgroundColor = null;
                if (!empty($lesson->colorCode)) {
                    $class = null;
                    $backgroundColor = $lesson->colorCode;
                } elseif ($lesson->isEnrolmentFirstlesson()) {
                    $class = 'first-lesson';
                } elseif ($lesson->getRootLesson()) {
                    $rootLesson = $lesson->getRootLesson();
                    if ($rootLesson->id !== $lesson->id) {
                        $class = 'lesson-rescheduled';
                    }
                    if ($rootLesson->teacherId !== $lesson->teacherId) {
                        $class = 'teacher-substituted';
                    }
                }

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
            ];
        }
        unset($lesson);
        return $events;
    }

    public function actionRenderCalendarTime($day, $view)
    {
        $userId = Yii::$app->user->id;
        $userLocation = UserLocation::findOne(['user_id' => $userId]);
        $locationId = $userLocation->location_id;
        if ($view === 'agendaDay') {
            $locationAvailability = LocationAvailability::findOne(['locationId' => $locationId,
                'day' => $day]);
            $calendarTime['from_time'] = $locationAvailability->fromTime;
            $calendarTime['to_time'] = $locationAvailability->toTime;
        } else {
            $minAvailability = LocationAvailability::find()
                ->andWhere(['locationId' => $locationId])
                ->orderBy(['fromTime' => SORT_ASC])
                ->one();
            $maxAvailability = LocationAvailability::find()
                ->andWhere(['locationId' => $locationId])
                ->orderBy(['toTime' => SORT_DESC])
                ->one();
            $calendarTime['from_time'] = $minAvailability->fromTime;
            $calendarTime['to_time'] = $maxAvailability->toTime;
        }
	return $calendarTime;
    }
}
