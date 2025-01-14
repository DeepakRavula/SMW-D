<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use common\models\Lesson;
use common\models\Location;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Program;
use yii\helpers\Url;
use common\models\Holiday;
use common\models\TeacherAvailability;
use common\models\LocationAvailability;
use common\models\ClassroomUnavailability;
use common\models\Classroom;
use common\models\TeacherUnavailability;
use League\Period\Period;
use Carbon\Carbon;
use common\models\User;
use common\components\controllers\BaseController;
use Carbon\CarbonInterval;
use backend\models\search\ScheduleSearch;
use common\models\CustomerRecurringPayment;

/**
 * QualificationController implements the CRUD actions for Qualification model.
 */
class ScheduleController extends BaseController
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
                        'actions' => [
                            'index', 'render-day-events', 'render-classroom-events', 
                            'render-resources', 'render-classroom-resources', 'fetch-holiday-name'
                        ],
                        'roles' => ['manageSchedule'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return $this->redirect('/admin/sign-in/login');
                }
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'only' => ['render-day-events', 'render-classroom-events',
                   'render-resources', 'render-classroom-resources', 'fetch-holiday-name'],
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
       
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $currentDate = Carbon::now();
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

        return $this->render('index', [
            'locationAvailabilities'   => $locationAvailabilities,
            'scheduleVisibilities'     => $scheduleVisibilities,
            'locationId'               => $locationId,
            'searchModel' => $searchModel
        ]);
    }

    public function actionFetchHolidayName($date)
    {
        $holiday = Holiday::find()->andWhere(['DATE(date)' => $date])->one();
        $holidayResource = '';
        if (!empty($holiday)) {
            $holidayResource = ' (' . $holiday->description . ')';
        }
        $data = $this->renderAjax('title', [
            'name' => $holidayResource,
            'date' => $date,
        ]);
        return $data;
    }

    public function getLessons($date, $teacherId = null)
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $query = Lesson::find()
            ->location($locationId)
            ->scheduledOrRescheduled()
            ->isConfirmed()
            ->present()
            ->andWhere(['DATE(lesson.date)' => $date->format('Y-m-d')])
            ->notDeleted();
        if (!empty($teacherId)) {
            $query->andWhere(['lesson.teacherId' => $teacherId]);
        }
        $lessons = $query->all();
        return $lessons;
    }

    public function actionRenderClassroomResources()
    {
        $resources = [];
        $classrooms = Classroom::find()
                ->andWhere(['locationId' => Location::findOne(['slug' => Yii::$app->location])->id])
                ->notDeleted()
                ->all();
        foreach ($classrooms as $classroom) {
            $resources[] = [
                'id'    => $classroom->id,
                'title' => $classroom->name,
                'description' => $classroom->description,
            ];
        }
        return $resources;
    }

    public function actionRenderResources()
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $scheduleRequest = Yii::$app->request->get('ScheduleSearch');
        $teacherId = $scheduleRequest['teacherId'];
        $showAll = $scheduleRequest['showAll'];
        $programId = $scheduleRequest['programId'];
        $date = $scheduleRequest['date'];
        $date       = new \DateTime($date);
        $formatedDate = $date->format('Y-m-d');
        $formatedDay = $date->format('N');
        $resources = [];
        $query = User::find()
            ->joinWith(['teacherLessons' => function ($query) use ($formatedDate) {
                $query->andWhere(['DATE(lesson.date)' => $formatedDate]);
            }])
            ->joinWith(['userProfile' => function ($query) {
                $query->orderBy(['user_profile.firstname' => SORT_ASC]);
            }]);
            
        if ($showAll && empty($teacherId) && empty($programId)) {
            $availableUserQuery = User::find()
                ->joinWith(['availabilities' => function ($query) use ($formatedDay) {
                    $query->andWhere(['teacher_availability_day.day' => $formatedDay]);
                }])
                ->location($locationId);
            $query->union($availableUserQuery);
        }
        if (!$programId) {
            $query->location($locationId);
        }
        if (!empty($teacherId)) {
            $query->andWhere(['user.id' => $teacherId])
                ->location($locationId);
        } else if (!empty($programId)) {
            $query->teachers($programId, $locationId);
        }
        $teachers = $query->groupBy('user.id')->all();
        $resources = $this->setResources($teachers);
        if (empty($resources)) {
            if (!empty($teacherId)) {
                $resources[] = [
                    'id'    => '0',
                    'title' => 'Teacher not available today'
                ];
            } else if (!empty($programId)) {
                $resources[] = [
                    'id'    => '0',
                    'title' => 'No teacher available today for the selected program'
                ];
            } else if (empty($teacherId) && empty($programId)) {
                $resources[] = [
                    'id'    => '0',
                    'title' => 'No teacher available today'
                ];
            }
        }
        return $resources;
    }
    
    public function getTeacherAvailability($teacherId, $programId, $showAll, $date)
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $formatedDate = $date->format('Y-m-d');
        $availabilityQuery = TeacherAvailability::find()
            ->notDeleted()
            ->andWhere(['day' => $date->format('N')]);
        $availabilityQuery->joinWith(['userLocation' => function ($query) use ($teacherId, $programId, $locationId, $showAll, $formatedDate) {
            if (!$showAll) {
                $query->joinWith(['user' => function ($query) use ($formatedDate) {
                    $query->joinWith(['teacherLessons' => function ($query) use ($formatedDate) {
                        $query->andWhere(['DATE(lesson.date)' => $formatedDate]);
                    }]);
                }]);
            }
            if ($teacherId) {
                $query->andWhere(['user_location.user_id' => $teacherId]);
            } else if ($programId) {
                $query->joinWith(['qualifications'  => function ($query) use ($programId) {
                    $query->andWhere(['qualification.program_id' => $programId]);
                }]);
            }
            $query->andWhere(['user_location.location_id' => $locationId]);
        }]);
        $availabilities = $availabilityQuery->all();
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
                        if (empty($unavailability->fromDateTime) && empty($unavailability->toDateTime) || $unavailability->fromDateTime === 
                            $teachersAvailability->from_time && $unavailability->toDateTime === $teachersAvailability->to_time) {
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
        $dateObject = Carbon::parse($date);
        $availabilityStartDateTime = new \DateTime($teachersAvailability->from_time);
        $availabilityEndDateTime = new \DateTime($teachersAvailability->to_time);
        $availabilityStart = clone $dateObject->setTime($availabilityStartDateTime->format('H'), $availabilityStartDateTime->format('i'), 
            $availabilityStartDateTime->format('s'));
        $availabilityEnd = clone $dateObject->setTime($availabilityEndDateTime->format('H'), $availabilityEndDateTime->format('i'), 
            $availabilityEndDateTime->format('s'));

        $unavailabilityStartDateTime = new \DateTime($unavailability->fromDateTime);
        $unavailabilityEndDateTime = new \DateTime($unavailability->toDateTime);
        $unavailabilityStart = clone $dateObject->setTime($unavailabilityStartDateTime->format('H'), $unavailabilityStartDateTime->format('i'), 
            $unavailabilityStartDateTime->format('s'));
        $unavailabilityEnd = clone $dateObject->setTime($unavailabilityEndDateTime->format('H'), $unavailabilityEndDateTime->format('i'), 
            $unavailabilityEndDateTime->format('s'));
            
        if ($unavailabilityStart > $availabilityStart && $availabilityEnd >= $unavailabilityEnd) {
            $start = $availabilityStart;
            $end = $unavailabilityStart;
            $events[] = $this->setEvents($teachersAvailability, $start, $end);
            if ($unavailabilityEnd != $availabilityEnd) {
                $start = $unavailabilityEnd;
                $end = $availabilityEnd;
                $events[] = $this->setEvents($teachersAvailability, $start, $end);
            }
        } elseif ($unavailabilityStart <= $availabilityStart && $availabilityEnd > $unavailabilityEnd) {
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
    
    public function actionRenderDayEvents()
    {
        $events = [];
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $scheduleRequest = Yii::$app->request->get('ScheduleSearch');
        $teacherId = $scheduleRequest['teacherId'];
        $showAll = $scheduleRequest['showAll'];
        $programId = $scheduleRequest['programId'];
        $date = $scheduleRequest['date'];
        $date = Carbon::parse($date);
        $formatedDate = $date->format('Y-m-d');
        $teachersAvailabilities = $this->getTeacherAvailability($teacherId, $programId, $showAll, $date);
        $events = $this->getTeacherAvailabilityEvents($teachersAvailabilities, $date);
        $lessons = $this->getLessons($date, $teacherId);
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->duration);
            $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
            $title = $lesson->scheduleTitle;
            $class = $lesson->class;
            $backgroundColor = $lesson->colorCode;
            if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                $description = $this->renderAjax('group-lesson-description', [
                    'lesson' => $lesson,
                    'view' => Lesson::TEACHER_VIEW
                ]);
            } else {
                $description = $this->renderAjax('private-lesson-description', [
                    'title' => $title,
                    'lesson' => $lesson,
                    'view' => Lesson::TEACHER_VIEW
                ]);
            }

            $events[] = [
                'lessonId' => $lesson->id,
                'isOwing' => $lesson->student ? $lesson->student->customer->customerAccount->balance > 0 ? true:false : null,
                'resourceId' => $lesson->teacherId,
                'title' => $title,
                'start' => $lesson->date,
                'end' => $toTime->format('Y-m-d H:i:s'),
                'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
                'className' => $class,
                'backgroundColor' => $backgroundColor,
                'description' => $description,
                'isOnline' => $lesson->is_online,
                
            ];
        }
        unset($lesson);
        return $events;
    }

    public function actionRenderClassroomEvents($date)
    {
        $date = new \DateTime($date);
        $classroomUnavailabilities = ClassroomUnavailability::find()
            ->andWhere(['AND',
                ['<=', 'DATE(fromDate)', $date->format('Y-m-d')],
                ['>=', 'DATE(toDate)', $date->format('Y-m-d')]
            ])
            ->all();
        $locationAvailability = LocationAvailability::find()
            ->notDeleted()
            ->location(Location::findOne(['slug' => Yii::$app->location])->id)
            ->day($date->format('N'))
            ->scheduleVisibilityHours()
            ->one();
        if (empty($locationAvailability)) {
            $locationFromTime = LocationAvailability::DEFAULT_FROM_TIME;
            $locationToTime = LocationAvailability::DEFAULT_TO_TIME;
        } else {
            $locationFromTime = $locationAvailability->fromTime;
            $locationToTime = $locationAvailability->toTime;
        }
        $events = [];
        foreach ($classroomUnavailabilities as $classroomUnavailability) {
            list($fromTime['hours'], $fromTime['minutes'], $fromTime['seconds']) = explode(':', $locationFromTime);
            $start = $date->setTime($fromTime['hours'], $fromTime['minutes'], $fromTime['seconds']);
            $end = clone $date;
            list($toTime['hours'], $toTime['minutes'], $toTime['seconds']) = explode(':', $locationToTime);
            $end = $end->setTime($toTime['hours'], $toTime['minutes'], $toTime['seconds']);
            $events[] = [
                'resourceId' => $classroomUnavailability->classroomId,
                'title'      => '',
                'start'      => $start->format('Y-m-d H:i:s'),
                'end'        => $end->format('Y-m-d H:i:s'),
                'rendering'  => 'background',
            ];
        }
        $lessons = $this->getLessons($date);
        foreach ($lessons as &$lesson) {
            if (! empty($lesson->classroomId)) {
                $toTime = new \DateTime($lesson->date);
                $length = explode(':', $lesson->duration);
                $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
                $title = $lesson->scheduleTitle;
                $class = $lesson->class;
                $backgroundColor = $lesson->colorCode;
                if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                    $description = $this->renderAjax('group-lesson-description', [
                        'title' => $title,
                        'lesson' => $lesson,
                        'view' => Lesson::CLASS_ROOM_VIEW
                    ]);
                } else {
                    $description = $this->renderAjax('private-lesson-description', [
                    'title' => $title,
                    'lesson' => $lesson,
                    'view' => Lesson::CLASS_ROOM_VIEW
                                    ]);
                }
                $classroomId = $lesson->classroomId;

                $events[] = [
                    'id' => $lesson->id,
                    'resourceId' => $classroomId,
                    'title' => $title,
                    'start' => $lesson->date,
                    'end' => $toTime->format('Y-m-d H:i:s'),
                    'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
                    'className' => $class,
                    'backgroundColor' => $backgroundColor,
                    'description' => $description,
                ];
            }
        }
        unset($lesson);
        return $events;
    }

    public function setResources($teachers) 
    {
        $resources = [];
        foreach ($teachers as $teacher) {
            $resources[] = [
                'id'    => $teacher->id,
                'title' => $teacher->getPublicIdentity(),
            ];
        }
        return $resources;
    }
}