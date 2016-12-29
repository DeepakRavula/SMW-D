<?php

namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use common\models\Location;
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
               'only' => ['day-event', 'classroom-event'],
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
            ->all();
        $teachersAvailabilitiesAllDetails = ArrayHelper::toArray($teachersAvailabilities, [
            'common\models\TeacherAvailability' => [
                'id' => function ($teachersAvailability) {
                    return $teachersAvailability->userLocation->user_id;
                },
                'day',
                'from_time',
                'to_time',
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

        $teachersAvailabilitiesDetails = ArrayHelper::toArray($teachersAvailabilities, [
            'common\models\TeacherAvailability' => [
                'id' => function ($teachersAvailability) {
                    return $teachersAvailability->userLocation->user_id;
                },
                'day',
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
        $availableTeachersDetails = array_unique($teachersAvailabilitiesDetails, SORT_REGULAR);
        $availableTeachersDetails = array_values($availableTeachersDetails);

        $holidays = Holiday::find()->all();
        $currentDate = (new \DateTime())->format('Y-m-d');
        $events = $this->getDayEvents($currentDate, $slug);
        $classroomEvents = $this->getClassroomEvents($currentDate, $slug);
        $classrooms = Classroom::find()->all();
		$classroomResource = [];
			foreach ($classrooms as $classroom) {
				$classroomResource[] = [
					'id' => $classroom->id,
					'title' => $classroom->name,
				];
			}

		if ($locationId <= 9) {
            $location = Location::findOne(['id' => $locationId]);
            $from_time = $location->from_time;
            $to_time = $location->to_time;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('view', [
			'holidays' => $holidays,
			'teachersAvailabilitiesAllDetails' => $teachersAvailabilitiesAllDetails,
			'teachersAvailabilitiesDetails' => $teachersAvailabilitiesDetails,
			'availableTeachersDetails' => $availableTeachersDetails,
			'events' => $events,
            'slug' => $slug,
			'from_time' => $from_time,
			'to_time' => $to_time,
			'classroomResource' => $classroomResource,
			'classroomEvents' => $classroomEvents
		]);
    }

    public function actionDayEvent($date, $slug)
    {
        return $this->getDayEvents($date, $slug);
    }

    public function actionClassroomEvent($date, $slug)
    {
        return $this->getClassroomEvents($date, $slug);
    }

    public function getHolidayEvent($date)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $date);
        $events = [];
        $holiday = Holiday::find()
            ->andWhere(['holiday.date' => $date->format('Y-m-d 00:00:00')])
            ->one();
        if (!empty($holiday)) {
            $events[] = [
                'resourceId' => '0',
                'title' => '',
                'start' => $holiday->date,
                'end' => $date->format('Y-m-d 23:59:59'),
                'className' => 'holiday',
                'rendering' => 'background'
            ];
        }
        return $events;
    }

    public function getLessons($date, $slug)
    {
        $location = Location::find()->where(['like', 'slug', $slug])->one();
        $locationId = $location->id;
        $date = \DateTime::createFromFormat('Y-m-d', $date);
        $lessons = Lesson::find()
                ->joinWith(['course' => function ($query) use ($locationId) {
                    $query->andWhere(['locationId' => $locationId]);
                }])
                ->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
                ->between($date, $date)
                ->notDeleted()
                ->all();
        return $lessons;
    }

    public function getDayEvents($date, $slug)
    {
        $events = $this->getHolidayEvent($date);
        if (empty($events)) {
            $lessons = $this->getLessons($date, $slug);
            foreach ($lessons as &$lesson) {
                $toTime = new \DateTime($lesson->date);
                $length = explode(':', $lesson->duration);
                $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
                if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                    $title = $lesson->course->program->name.' ( '.$lesson->course->getEnrolmentsCount().' ) ';
                    $class = 'group-lesson';
                    $backgroundColor = null;
                    if (!empty($lesson->colorCode)) {
                        $class = null;
                        $backgroundColor = $lesson->colorCode;
                    }
                } else {
                    $title = $lesson->enrolment->student->fullName.' ( '.$lesson->course->program->name.' ) ';
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
                    $title = $title . '[ ' . $classroom . ' ]';
                }

                $events[] = [
                    'resourceId' => $lesson->teacherId,
                    'title' => $title,
                    'start' => $lesson->date,
                    'end' => $toTime->format('Y-m-d H:i:s'),
                    'url' => Url::to(['lesson/view', 'id' => $lesson->id]),
                    'className' => $class,
                    'backgroundColor' => $backgroundColor,
                ];
            }
            unset($lesson);
        }
        return $events;
    }

    public function getClassroomEvents($date, $slug)
    {
        $events = $this->getHolidayEvent($date);
        if (empty($events)) {
            $lessons = $this->getLessons($date, $slug);
            foreach ($lessons as &$lesson) {
                if(! empty($lesson->classroomId)) {
                    $toTime = new \DateTime($lesson->date);
                    $length = explode(':', $lesson->duration);
                    $toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
                    if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
                        $title = $lesson->course->program->name.' ( '.$lesson->course->getEnrolmentsCount().' ) ';
                        $class = 'group-lesson';
                        $backgroundColor = null;
                        if (!empty($lesson->colorCode)) {
                            $class = null;
                            $backgroundColor = $lesson->colorCode;
                        }
                    } else {
                        $title = $lesson->enrolment->student->fullName.' ( '.$lesson->course->program->name.' ) ';
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
                    $classroom = $lesson->classroom->name;
                    $classroomId = $lesson->classroomId;
                    $title = $title . '[ ' . $lesson->teacher->publicIdentity . ' ]';
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
