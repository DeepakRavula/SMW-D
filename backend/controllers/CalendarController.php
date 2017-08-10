<?php

namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use common\models\Location;
use common\models\LocationAvailability;
use common\models\Lesson;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Program;
use yii\helpers\Url;
use common\models\Holiday;
use common\models\TeacherAvailability;
use common\models\Classroom;

/**
 * QualificationController implements the CRUD actions for Qualification model.
 */
class CalendarController extends Controller
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
    public function actionView($slug)
    {
       	$location = Location::find()->where(['like', 'slug', $slug])->one();
        $locationId = $location->id;
        $this->layout = 'guest';
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
        if ($locationId <= 9) {
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
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $this->render('view', [
            'availableTeachersDetails' => $availableTeachersDetails,
            'locationAvailabilities'   => $locationAvailabilities,
            'from_time'                => $from_time,
            'to_time'                  => $to_time,
        ]);
    }

    public function getHolidayEvent($date, $locationId)
    {
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

    public function getHolidayResources($date, $locationId)
    {
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

    public function getLessons($date, $locationId)
    {
        $lessons = Lesson::find()
                ->joinWith(['course' => function ($query) use($locationId) {
                    $query->andWhere(['course.locationId' => $locationId]);
                }])
                ->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED]]])
				->isConfirmed()
                ->between($date, $date)
                ->notDeleted()
                ->all();
        return $lessons;
    }

    public function actionRenderClassroomResources($date, $locationId)
    {
        $date      = \DateTime::createFromFormat('Y-m-d', $date);
        $resources = $this->getHolidayResources($date, $locationId);
        if (empty($resources)) {
            $classrooms = Classroom::find()->all();
            foreach ($classrooms as $classroom) {
                $resources[] = [
                    'id'    => $classroom->id,
                    'title' => $classroom->name,
                ];
            }
        }
        return $resources;
    }

    public function actionRenderResources($date, $locationId)
    {
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
        $resources  = $this->getHolidayResources($date, $locationId);
        if (empty($resources)) {
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
        return $resources;
    }

    public function actionRenderDayEvents($date, $locationId)
    {
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
        $events     = $this->getHolidayEvent($date, $locationId);
        if (empty($events)) {
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
            $lessons = $this->getLessons($date, $locationId);
            foreach ($lessons as &$lesson) {
                $toTime = new \DateTime($lesson->date);
                $length = explode(':', $lesson->fullDuration);
                $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
                $class = $lesson->class;
                $backgroundColor = $lesson->colorCode;
                if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                    $title = $lesson->course->program->name.' ( '.$lesson->course->getEnrolmentsCount().' ) ';
                } else {
                    $title = $lesson->enrolment->student->fullName.' ( '.$lesson->course->program->name.' ) ';
                }
                if(! empty($lesson->classroomId)) {
                    $classroom = $lesson->classroom->name;
                    $title = $title . '[ ' . $classroom . ' ]';
                }

                $events[] = [
                    'resourceId' => $lesson->teacherId,
                    'title' => $title,
                    'start' => $lesson->date,
                    'end' => $toTime->format('Y-m-d H:i:s'),
                    'className' => $class,
                    'backgroundColor' => $backgroundColor,
                ];
            }
            unset($lesson);
        }
        return $events;
    }

    public function actionRenderClassroomEvents($date, $locationId)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $date);
        $events = $this->getHolidayEvent($date, $locationId);
        if (empty($events)) {
            $programId = null;
            $teacherId = null;
            $lessons = $this->getLessons($date, $programId, $teacherId, $locationId);
            foreach ($lessons as &$lesson) {
                if(! empty($lesson->classroomId)) {
                    $toTime = new \DateTime($lesson->date);
                    $length = explode(':', $lesson->fullDuration);
                    $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
                    $class = $lesson->class;
                    $backgroundColor = $lesson->colorCode;
                    if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                        $title = $lesson->teacher->publicIdentity . ' [' . $lesson->course->program->name.' ( '.$lesson->course->getEnrolmentsCount().' ) ' . ']';
                    } else {
                        $title = $lesson->teacher->publicIdentity . ' [' . $lesson->enrolment->student->fullName.' ( '.$lesson->course->program->name.' ) ' . ']';
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
