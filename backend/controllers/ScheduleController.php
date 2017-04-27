<?php

namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use common\models\Lesson;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Program;
use yii\helpers\Url;
use common\models\Holiday;
use common\models\TeacherAvailability;
use common\models\LocationAvailability;
use common\models\Classroom;

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
        $locationId             = Yii::$app->session->get('location_id');
        $teachersAvailabilities = TeacherAvailability::find()
            ->joinWith(['userLocation' => function ($query) use ($locationId) {
                $query->joinWith(['userProfile'])
                ->where(['user_location.location_id' => $locationId]);
            }])
            ->orderBy(['teacher_availability_day.id' => SORT_DESC])
            ->groupBy('teacher_location_id')
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
            ->where(['locationId' => $locationId])
            ->all();
        $locationAvailability = LocationAvailability::findOne(['locationId' => $locationId,
            'day' => $date->format('N')]);
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

    public function getHolidayEvent($date)
    {
        $locationId = Yii::$app->session->get('location_id');
        $events     = [];
        $locationAvailability = LocationAvailability::findOne(['locationId' => $locationId,
            'day' => $date->format('N')]);
        if (empty($locationAvailability)) {
            $events[] = [
                'resourceId' => '0',
                'title'      => '',
                'start'      => $date->format('Y-m-d 00:00:00'),
                'end'        => $date->format('Y-m-d 23:59:59'),
                'className'  => 'holiday',
                'rendering'  => 'background'
            ];
        } else {
            $holiday    = Holiday::find()
                ->andWhere(['holiday.date' => $date->format('Y-m-d 00:00:00')])
                ->one();
            if (!empty($holiday)) {
                $events[] = [
                    'resourceId' => '0',
                    'title'      => '',
                    'start'      => $holiday->date,
                    'end'        => $date->format('Y-m-d 23:59:59'),
                    'className'  => 'holiday',
                    'rendering'  => 'background'
                ];
            }
        }
        return $events;
    }

    public function getHolidayResources($date)
    {
        $locationId = Yii::$app->session->get('location_id');
        $locationAvailability = LocationAvailability::findOne(['locationId' => $locationId,
            'day' => $date->format('N')]);
        $resources  = [];
        if (empty($locationAvailability)) {
            $resources[] = [
                'id'    => '0',
                'title' => 'Holiday',
            ];
        } else {
            $holiday    = Holiday::find()
                ->andWhere(['holiday.date' => $date->format('Y-m-d 00:00:00')])
                ->one();
            if (!empty($holiday)) {
                $resources[] = [
                    'id'    => '0',
                    'title' => 'Holiday',
                ];
            }
        }
        return $resources;
    }

    public function getLessons($date, $programId, $teacherId)
    {
        $lessons = Lesson::find()
                ->joinWith(['course' => function ($query) use($programId, $teacherId) {
                    $query->andWhere(['course.locationId' => Yii::$app->session->get('location_id')]);
                    if(!empty($programId) && $programId != 'undefined') {
                        $query->andWhere(['course.programId' => $programId]);
                    }
                    if(!empty($teacherId) && $teacherId != 'undefined') {
                        $query->andWhere(['course.teacherId' => $teacherId]);
                    }
                }])
                ->andWhere(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED]])
                ->between($date, $date)
                ->notDeleted()
                ->all();
        return $lessons;
    }

    public function actionRenderClassroomResources($date)
    {
        $date      = \DateTime::createFromFormat('Y-m-d', $date);
        $resources = $this->getHolidayResources($date);
        if (empty($resources)) {
            $classrooms = Classroom::find()
				->andWhere(['locationId' => Yii::$app->session->get('location_id')])
				->all();
            foreach ($classrooms as $classroom) {
                $resources[] = [
                    'id'    => $classroom->id,
                    'title' => $classroom->name,
                ];
            }
        }
        return $resources;
    }

    public function actionRenderResources($date, $programId, $teacherId)
    {
        $locationId = Yii::$app->session->get('location_id');
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
        $resources  = $this->getHolidayResources($date);
        if (empty($resources)) {
            if ((empty($teacherId) && empty($programId)) || ($teacherId == 'undefined')
                && ($programId == 'undefined')) {
                $teachersAvailabilities = TeacherAvailability::find()
                            ->joinWith(['userLocation' => function ($query) use ($locationId) {
                                $query->where(['user_location.location_id' => $locationId]);
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
            }
            if (!empty($teacherId) && $teacherId != 'undefined') {
                $teachersAvailabilities = TeacherAvailability::find()
                        ->joinWith(['userLocation' => function ($query) use ($teacherId) {
                            $query->where(['user_location.user_id' => $teacherId]);
                        }])
                        ->andWhere(['day' => $date->format('N')])
                        ->groupBy(['teacher_location_id'])
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
                        'title' => 'Selected Teacher Not Available Today'
                    ];
                }
            } else if (!empty($programId) && $programId != 'undefined') {
                $teachersAvailabilities = TeacherAvailability::find()
                        ->joinWith(['userLocation' => function ($query) use ($locationId, $programId) {
                            $query->where(['user_location.location_id' => $locationId]);
                            $query->joinWith(['qualifications'  => function ($query) use ($programId) {
                                $query->andWhere(['qualification.program_id' => $programId]);
                            }]);
                        }])
                        ->andWhere(['day' => $date->format('N')])
                        ->groupBy(['teacher_location_id'])
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
            }
        }
        return $resources;
    }

    public function actionRenderDayEvents($date, $programId, $teacherId)
    {
        $locationId = Yii::$app->session->get('location_id');
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
        $events     = $this->getHolidayEvent($date);
        if (empty($events)) {
            if ((empty($teacherId) && empty($programId)) || ($teacherId == 'undefined')
                && ($programId == 'undefined')) {
                $teachersAvailabilities = TeacherAvailability::find()
                    ->where(['day' => $date->format('N')])
                    ->all();

                foreach ($teachersAvailabilities as $teachersAvailability) {
                    $start = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') .
                        ' ' . $teachersAvailability->from_time);
                    $end   = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') .
                        ' ' . $teachersAvailability->to_time);
                    $events[] = [
                        'resourceId' => $teachersAvailability->teacher->id,
                        'title'      => '',
                        'start'      => $start->format('Y-m-d H:i:s'),
                        'end'        => $end->format('Y-m-d H:i:s'),
                        'rendering'  => 'background',
                    ];
                }
            }
            if (!empty($teacherId) && $teacherId != 'undefined') {
                $teachersAvailabilities = TeacherAvailability::find()
                    ->joinWith(['userLocation' => function ($query) use ($teacherId) {
                        $query->where(['user_location.user_id' => $teacherId]);
                    }])
                    ->andWhere(['day' => $date->format('N')])
                    ->all();

                foreach ($teachersAvailabilities as $teachersAvailability) {
                    $start = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') .
                        ' ' . $teachersAvailability->from_time);
                    $end   = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') .
                        ' ' . $teachersAvailability->to_time);
                    $events[] = [
                        'resourceId' => $teachersAvailability->teacher->id,
                        'title'      => '',
                        'start'      => $start->format('Y-m-d H:i:s'),
                        'end'        => $end->format('Y-m-d H:i:s'),
                        'rendering'  => 'background',
                    ];
                }
            } else if (!empty($programId) && $programId != 'undefined') {
                $teachersAvailabilities = TeacherAvailability::find()
                    ->joinWith(['userLocation' => function ($query) use ($locationId, $programId) {
                        $query->where(['user_location.location_id' => $locationId]);
                        $query->joinWith(['qualifications'  => function ($query) use ($programId) {
                            $query->andWhere(['qualification.program_id' => $programId]);
                        }]);
                    }])
                    ->andWhere(['day' => $date->format('N')])
                    ->all();

                foreach ($teachersAvailabilities as $teachersAvailability) {
                    $start = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') .
                        ' ' . $teachersAvailability->from_time);
                    $end   = \DateTime::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') .
                        ' ' . $teachersAvailability->to_time);
                    $events[] = [
                        'resourceId' => $teachersAvailability->teacher->id,
                        'title'      => '',
                        'start'      => $start->format('Y-m-d H:i:s'),
                        'end'        => $end->format('Y-m-d H:i:s'),
                        'rendering'  => 'background',
                    ];
                }
            }
            $lessons = $this->getLessons($date, $programId, $teacherId);
            foreach ($lessons as &$lesson) {
                $toTime = new \DateTime($lesson->date);
                $length = explode(':', $lesson->duration);
                $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
                if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
					$content = $lesson->course->program->name.' ( '.$lesson->course->getEnrolmentsCount().' ) '; 
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
                    } else if ($lesson->status === Lesson::STATUS_MISSED) {
                        $class = 'lesson-missed';
                    } else if($lesson->isEnrolmentFirstlesson()) {
                        $class = 'first-lesson';
                    } else if ($lesson->getRootLesson()) {
                        $class = 'lesson-rescheduled';
                        $rootLesson = $lesson->getRootLesson();
                        if ($rootLesson->teacherId !== $lesson->teacherId) {
                            $class = 'teacher-substituted';
                        }
                    }
                }
                if(! empty($lesson->classroomId)) {
                    $classroom = $lesson->classroom->name;
					$content = $title . '[ ' . $classroom . ' ]';
                }

                $events[] = [
                    'resourceId' => $lesson->teacherId,
                    'title' => $title,
                    'start' => $lesson->date,
                    'end' => $toTime->format('Y-m-d H:i:s'),
                    'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
                    'className' => $class,
                    'backgroundColor' => $backgroundColor,
					'content' => $content,
                ];
            }
            unset($lesson);
        }
        return $events;
    }

    public function actionRenderClassroomEvents($date)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $date);
        $events = $this->getHolidayEvent($date);
        if (empty($events)) {
            $programId = null;
            $teacherId = null;
            $lessons = $this->getLessons($date, $programId, $teacherId);
            foreach ($lessons as &$lesson) {
                if(! empty($lesson->classroomId)) {
                    $toTime = new \DateTime($lesson->date);
                    $length = explode(':', $lesson->duration);
                    $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
                    if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                        $title = $lesson->teacher->publicIdentity . ' [' . $lesson->course->program->name.' ( '.$lesson->course->getEnrolmentsCount().' ) ' . ']';
                        $class = 'group-lesson';
                        $backgroundColor = null;
                        if (!empty($lesson->colorCode)) {
                            $class = null;
                            $backgroundColor = $lesson->colorCode;
                        }
                    } else {
                        $title = $lesson->teacher->publicIdentity . ' [' . $lesson->enrolment->student->fullName.' ( '.$lesson->course->program->name.' ) ' . ']';
                        $class = 'private-lesson';
                        $backgroundColor = null;
                        if (!empty($lesson->colorCode)) {
                            $class = null;
                            $backgroundColor = $lesson->colorCode;
                        } else if ($lesson->status === Lesson::STATUS_MISSED) {
                            $class = 'lesson-missed';
                        } else if($lesson->isEnrolmentFirstlesson()) {
                            $class = 'first-lesson';
                        } else if ($lesson->getRootLesson()) {
                            $class = 'lesson-rescheduld';
                            $rootLesson = $lesson->getRootLesson();
                            if ($rootLesson->teacherId !== $lesson->teacherId) {
                                $class = 'teacher-substituted';
                            }
                        }
                    }
                    $classroomId = $lesson->classroomId;
                    $events[] = [
                        'resourceId' => $classroomId,
                        'title' => $title,
                        'start' => $lesson->date,
                        'end' => $toTime->format('Y-m-d H:i:s'),
                        'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
                        'className' => $class,
                        'backgroundColor' => $backgroundColor,
                    ];
                }
            }
            unset($lesson);
        }
        return $events;
    }
}
