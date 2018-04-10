<?php

namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
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
                        'actions' => ['index', 'render-day-events', 'render-classroom-events','render-resources', 'render-classroom-resources', 'fetch-holiday-name'],
                        'roles' => ['manageSchedule'],
                    ],
                ],
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
        $locationId             = Location::findOne(['slug' => Yii::$app->location])->id;
        $teachersAvailabilities = TeacherAvailability::find()
            ->joinWith(['userLocation' => function ($query) use ($locationId) {
                $query->joinWith(['userProfile'])
                ->andWhere(['user_location.location_id' => $locationId]);
            }])
            ->orderBy(['teacher_availability_day.id' => SORT_DESC])
           ->orderBy(['user_profile.firstname' => SORT_ASC])
             ->groupBy(['teacher_availability_day.id','teacher_location_id'])
            ->all();
        $availableTeachersDetails = ArrayHelper::toArray($teachersAvailabilities, [
            'common\models\TeacherAvailability' => [
                'id' => function ($teachersAvailability) {
                    return $teachersAvailability->userLocation->user_id;
                },
                'name' => function ($teachersAvailability) {
                    return $teachersAvailability->teacher->getPublicIdentity();
                },
                'programs' => function ($teachersAvailability) {
                    $qualifications = $teachersAvailability->userLocation->qualifications;
                    $programs = [];
                    foreach ($qualifications as $qualification) {
                        $programs[] = $qualification->program_id;
                    }
                    return $programs;
                },
            ],
        ]);

        $date = new \DateTime();
        $locationAvailabilities = LocationAvailability::find()
            ->location($locationId)
            ->scheduleVisibilityHours()
            ->all();
        $locationAvailability = LocationAvailability::findOne(['locationId' => $locationId,'day' => $date->format('N'),'type' => LocationAvailability::TYPE_SCHEDULE_TIME]);
        if (empty($locationAvailability)) {
            $from_time = LocationAvailability::DEFAULT_FROM_TIME;
            $to_time   = LocationAvailability::DEFAULT_TO_TIME;
        } else {
            $from_time = $locationAvailability->fromTime;
            $to_time   = $locationAvailability->toTime;
        }

        return $this->render('index', [
            'availableTeachersDetails' => $availableTeachersDetails,
            'locationAvailabilities'   => $locationAvailabilities,
            'from_time'                => $from_time,
            'to_time'                  => $to_time,
        ]);
    }

    public function actionFetchHolidayName($date)
    {
        $holiday = Holiday::findOne(['DATE(date)' => $date]);
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

    public function getLessons($date, $teacherId)
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $query = Lesson::find()
            ->location($locationId)
            ->scheduledOrRescheduled()
            ->isConfirmed()
            ->present()
            ->andWhere(['DATE(lesson.date)' => $date->format('Y-m-d')])
            ->notDeleted();
        if (!empty($teacherId) && $teacherId != 'undefined') {
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

    public function actionRenderResources($date, $programId, $teacherId)
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
        $formatedDate = $date->format('Y-m-d');
        $formatedDay = $date->format('N');
        $query = User::find()
                ->notDeleted()
                ->active()
                ->joinWith(['teacherLessons' => function ($query) use ($formatedDate) {
                    $query->isConfirmed()->notDeleted()->scheduledOrRescheduled()
                        ->andWhere(['DATE(lesson.date)' => $formatedDate]);
                }]);
        if ((empty($teacherId) && empty($programId)) || ($teacherId == 'undefined')
            && ($programId == 'undefined')) {
            $teachersAvailabilities = TeacherAvailability::find()
                        ->joinWith(['userLocation' => function ($query) use ($locationId) {
                            $query->andWhere(['user_location.location_id' => $locationId]);
                        }])
                        ->andWhere(['day' => $date->format('N')])
                        ->all();
            if (!empty($teachersAvailabilities)) {
                foreach ($teachersAvailabilities as $teachersAvailability) {
                    $resources[] = [
                        'id'    => $teachersAvailability->teacher->id,
                        'title' => $teachersAvailability->teacher->getPublicIdentity(),
                    ];
                }
            } else {
                $resources[] = [
                    'id'    => '0',
                    'title' => 'No Teacher Available Today'
                ];
            }
            $query->allTeachers()->location($locationId);
        }
        if (!empty($teacherId) && $teacherId != 'undefined') {
            $teachersAvailabilities = TeacherAvailability::find()
                    ->joinWith(['userLocation' => function ($query) use ($teacherId) {
                        $query->andWhere(['user_location.user_id' => $teacherId]);
                    }])
                    ->andWhere(['day' => $date->format('N')])
                    ->groupBy(['teacher_availability_day.id','teacher_location_id'])
                    ->all();
            if (!empty($teachersAvailabilities)) {
                foreach ($teachersAvailabilities as $teachersAvailability) {
                    $resources[] = [
                        'id'    => $teachersAvailability->teacher->id,
                        'title' => $teachersAvailability->teacher->getPublicIdentity(),
                    ];
                }
            }
            $query->andWhere(['user.id' => $teacherId])->location($locationId);
        } elseif (!empty($programId) && $programId != 'undefined') {
            $teachersAvailabilities = TeacherAvailability::find()
                    ->joinWith(['userLocation' => function ($query) use ($locationId, $programId) {
                        $query->andWhere(['user_location.location_id' => $locationId]);
                        $query->joinWith(['qualifications'  => function ($query) use ($programId) {
                            $query->andWhere(['qualification.program_id' => $programId]);
                        }]);
                    }])
                    ->andWhere(['day' => $date->format('N')])
                    ->groupBy(['teacher_availability_day.id','teacher_location_id'])
                    ->all();
            if (!empty($teachersAvailabilities)) {
                foreach ($teachersAvailabilities as $teachersAvailability) {
                    $resources[] = [
                        'id'    => $teachersAvailability->teacher->id,
                        'title' => $teachersAvailability->teacher->getPublicIdentity(),
                    ];
                }
            } else {
                $resources[] = [
                    'id'    => '0',
                    'title' => 'No Teacher Available Today for the Selected Program'
                ];
            }
            $query->teachers($programId, $locationId);
        }
        $teachers = $query->joinWith(['userLocation' => function ($query) use ($formatedDay) {
                        $query->joinWith(['teacherAvailabilities' => function ($query) use ($formatedDay) {
                            $query->andWhere(['NOT', ['teacher_availability_day.day' => $formatedDay]]);
                        }]);
                    }])
                    ->all();
        foreach ($teachers as $teacher) {
            $resources[] = [
                'id'    => $teacher->id,
                'title' => $teacher->getPublicIdentity()
            ];
        }
        if (empty($resources)) {
            $resources[] = [
                'id'    => '0',
                'title' => 'Selected Teacher Not Available Today'
            ];
        }
        return $resources;
    }
    
    public function getTeacherAvailability($teacherId, $date)
    {
        $availabilities = TeacherAvailability::find()
            ->joinWith(['userLocation' => function ($query) use ($teacherId) {
                $query->andWhere(['user_location.user_id' => $teacherId]);
            }])
            ->andWhere(['day' => $date->format('N')])
            ->all();
        return $availabilities;
    }
    
    public function getTeacherUnavailability($teacherAvailability, $date)
    {
        $unavailability = TeacherUnavailability::find()
            ->andWhere(['teacherId' => $teacherAvailability->teacher->id])
            ->overlap($teacherAvailability, $date)
            ->one();
        return $unavailability;
    }
    public function getTeacherAvailabilityEvents($teachersAvailability, $unavailability, $date)
    {
        $events = [];
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
    public function getRegularAvailability($teachersAvailability, $date)
    {
        $startTime = Carbon::parse($teachersAvailability->from_time);
        $start = $date->setTime($startTime->hour, $startTime->minute, $startTime->second);
        $endTime = Carbon::parse($teachersAvailability->to_time);
        $end = clone $date;
        $end = $end->setTime($endTime->hour, $endTime->minute, $endTime->second);
        $events[] = [
            'resourceId' => $teachersAvailability->teacher->id,
            'title'      => '',
            'start'      => $start->format('Y-m-d H:i:s'),
            'end'        => $end->format('Y-m-d H:i:s'),
            'rendering'  => 'background',
        ];
        return $events;
    }
    public function actionRenderDayEvents($date, $programId, $teacherId)
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $date = Carbon::parse($date);
        $events = [];
        if ((empty($teacherId) && empty($programId)) || ($teacherId == 'undefined')
            && ($programId == 'undefined')) {
            $teachersAvailabilities = TeacherAvailability::find()
                ->joinWith(['userLocation' => function ($query) use ($locationId) {
                    $query->andWhere(['user_location.location_id' => $locationId]);
                }])
                ->andWhere(['day' => $date->dayOfWeek])
                ->all();
            foreach ($teachersAvailabilities as $teachersAvailability) {
                $unavailability = $this->getTeacherUnavailability($teachersAvailability, $date);
                if (!empty($unavailability)) {
                    if (empty($unavailability->fromTime) && empty($unavailability->toTime)) {
                        continue;
                    } else {
                        $events = array_merge($events, $this->getTeacherAvailabilityEvents($teachersAvailability, $unavailability, $date));
                    }
                } else {
                    $startTime = Carbon::parse($teachersAvailability->from_time);
                    $start = $date->setTime($startTime->hour, $startTime->minute, $startTime->second);
                    $endTime = Carbon::parse($teachersAvailability->to_time);
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
        }
        if (!empty($teacherId) && $teacherId != 'undefined') {
            $teachersAvailabilities = $this->getTeacherAvailability($teacherId, $date);

            foreach ($teachersAvailabilities as $teachersAvailability) {
                $unavailability = $this->getTeacherUnavailability($teachersAvailability, $date);
                if (!empty($unavailability)) {
                    if (empty($unavailability->fromTime) && empty($unavailability->toTime)) {
                        continue;
                    } else {
                        $events = array_merge($events, $this->getTeacherAvailabilityEvents($teachersAvailability, $unavailability, $date));
                    }
                } else {
                    $events = $this->getRegularAvailability($teachersAvailability, $date);
                }
            }
        } elseif (!empty($programId) && $programId != 'undefined') {
            $teachersAvailabilities = TeacherAvailability::find()
                ->joinWith(['userLocation' => function ($query) use ($locationId, $programId) {
                    $query->andWhere(['user_location.location_id' => $locationId]);
                    $query->joinWith(['qualifications'  => function ($query) use ($programId) {
                        $query->andWhere(['qualification.program_id' => $programId]);
                    }]);
                }])
                ->andWhere(['day' => $date->format('N')])
                ->all();

            foreach ($teachersAvailabilities as $teachersAvailability) {
                $unavailability = $this->getTeacherUnavailability($teachersAvailability, $date);
                if (!empty($unavailability)) {
                    if (empty($unavailability->fromTime) && empty($unavailability->toTime)) {
                        continue;
                    } else {
                        $events = array_merge($events, $this->getTeacherAvailabilityEvents($teachersAvailability, $unavailability, $date));
                    }
                } else {
                    $startTime = Carbon::parse($teachersAvailability->from_time);
                    $start = $date->setTime($startTime->hour, $startTime->minute, $startTime->second);
                    $endTime = Carbon::parse($teachersAvailability->to_time);
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
        }
        $lessons = $this->getLessons($date, $teacherId);
        foreach ($lessons as &$lesson) {
            $toTime = new \DateTime($lesson->date);
            $length = explode(':', $lesson->fullDuration);
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
                'resourceId' => $lesson->teacherId,
                'title' => $title,
                'start' => $lesson->date,
                'end' => $toTime->format('Y-m-d H:i:s'),
                'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
                'className' => $class,
                'backgroundColor' => $backgroundColor,
                'description' => $description,
            ];
        }
        unset($lesson);
        return $events;
    }

    public function actionRenderClassroomEvents($date)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $date);
        $classroomUnavailabilities = ClassroomUnavailability::find()
            ->andWhere(['AND',
                ['<=', 'DATE(fromDate)', $date->format('Y-m-d')],
                ['>=', 'DATE(toDate)', $date->format('Y-m-d')]
            ])
            ->all();
        $locationAvailability = LocationAvailability::find()
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
        $teacherId = null;
        $lessons = $this->getLessons($date, $teacherId);
        foreach ($lessons as &$lesson) {
            if (! empty($lesson->classroomId)) {
                $toTime = new \DateTime($lesson->date);
                $length = explode(':', $lesson->fullDuration);
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
}
