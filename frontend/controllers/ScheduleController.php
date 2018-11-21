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
                        'actions' => ['index'],
                        'roles' => ['staffmember'],
                    ],
                ],
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
        $holiday = Holiday::findOne(['DATE(date)' => $date->format('Y-m-d')]);
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
             ->present()  
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
            ->one();
        return $unavailability;
    }

    public function getTeacherAvailabilityEvents($teachersAvailabilities, $date)
    {
        $events = [];
        foreach ($teachersAvailabilities as $teachersAvailability) {
            $unavailability = $this->getTeacherUnavailability($teachersAvailability, $date);
            if (!empty($unavailability)) {
                if (empty($unavailability->fromTime) && empty($unavailability->toTime) || $unavailability->fromTime === 
                    $teachersAvailability->from_time && $unavailability->toTime === $teachersAvailability->to_time) {
                    continue;
                } else {
                    $events = array_merge($events, $this->getAvailabilityEvents($teachersAvailability, $unavailability, $date));
                }
            } else {
                $events[] = $this->getRegularAvailability($teachersAvailability, $date);
            }
        }
        return $events;
    }

    public function getAvailabilityEvents($teachersAvailability, $unavailability, $date)
    {
        $availabilityStart = Carbon::parse($teachersAvailability->from_time);
        $availabilityEnd = Carbon::parse($teachersAvailability->to_time);
        $availabilityDiff = $availabilityStart->diff($availabilityEnd);
        $availabilityInterval = CarbonInterval::hour($availabilityDiff->h)->minutes($availabilityDiff->i)->seconds($availabilityDiff->s);
        
        $unavailabilityStart = Carbon::parse($unavailability->fromTime);
        $unavailabilityEnd = Carbon::parse($unavailability->toTime);
        $unavailabilityDiff = $unavailabilityStart->diff($unavailabilityEnd);
        $unavailabilityInterval = CarbonInterval::hour($unavailabilityDiff->h)->minutes($unavailabilityDiff->i)->seconds($unavailabilityDiff->s);
            
        $availabilityPeriods = Period::createFromDuration(
            $teachersAvailability->from_time,
            
            $availabilityInterval
            
        );
        $unavailabilityPeriods  = Period::createFromDuration(
            $unavailability->fromTime,
    
            $unavailabilityInterval
    
        );
        
        $overlapPeriod = $availabilityPeriods->overlaps($unavailabilityPeriods);
        if ($overlapPeriod) {
            $events = [];
            $availabilities = $availabilityPeriods->diff($unavailabilityPeriods);
            foreach ($availabilities as $availability) {
                if ($availability->getStartDate()->format('H:i:s') >= $teachersAvailability->from_time &&
                    $availability->getEndDate()->format('H:i:s') <= $teachersAvailability->to_time) {
                    $startTime = $availability->getStartDate()->format('Y-m-d H:i:s');
                    $startTime = Carbon::parse($startTime);
                    $start = $date->setTime($startTime->hour, $startTime->minute, $startTime->second);
                    $endTime = $availability->getEndDate()->format('Y-m-d H:i:s');
                    $endTime = Carbon::parse($endTime);
                    $end = clone $date;
                    $end = $end->setTime($endTime->hour, $endTime->minute, $endTime->second);
                    $events[] = [
                        'resourceId' => $teachersAvailability->teacher->id,
                        'title'      => '',
                        'start'      => $start->format('Y-m-d H:i:s'),
                        'end'        => $end->format('Y-m-d H:i:s'),
                        'rendering'  => 'background',
                    ];
                }
            }
        } else {
            $events = $this->getRegularAvailability($teachersAvailability, $date);
        }
        return $events;
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
}
