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
use yii\helpers\Url;
use common\models\TeacherAvailability;
use common\models\LocationAvailability;
use common\models\Classroom;
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
		$teacherId = Yii::$app->user->id; 
		$teacherLocation = UserLocation::findOne(['user_id' => $teacherId]);
        $locationId = $teacherLocation->location_id;
        $teachersAvailabilities = TeacherAvailability::find()
            ->joinWith(['userLocation' => function ($query) use ($locationId, $teacherId) {
                $query->joinWith(['userProfile'])
                ->where(['user_location.location_id' => $locationId, 'user_location.user_id' => $teacherId]);
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

    public function getLessons($teacherId)
    {
		$teacherLocation = UserLocation::findOne(['user_id' => $teacherId]);
        $locationId = $teacherLocation->location_id;
        $lessons = Lesson::find()
			->joinWith(['course' => function ($query) use($locationId) {
				$query->andWhere(['course.locationId' => $locationId]);
			}])
			->andWhere(['lesson.teacherId' => $teacherId])
			->andWhere(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED]])
			->notDeleted()
			->all();
        return $lessons;
    }

    public function actionRenderClassroomResources($teacherId)
    {
		$teacherLocation = UserLocation::findOne(['user_id' => $teacherId]);
        $locationId = $teacherLocation->location_id;
		$classrooms = Classroom::find()
			->joinWith(['lessons' => function($query) use($teacherId) {
				$query->andWhere(['lesson.teacherId' => $teacherId]);
			}])
			->andWhere(['locationId' => $locationId])
			->all();
		foreach ($classrooms as $classroom) {
			$resources[] = [
				'id'    => $classroom->id,
				'title' => $classroom->name,
			];
		}
        return $resources;
    }

    public function actionRenderDayEvents($teacherId)
    {
		$teachersAvailabilities = TeacherAvailability::find()
			->joinWith(['userLocation' => function ($query) use ($teacherId) {
				$query->where(['user_location.user_id' => $teacherId]);
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
		$lessons = $this->getLessons($teacherId);
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

    public function actionRenderClassroomEvents($teacherId)
    {
		$teacherLocation = UserLocation::findOne(['user_id' => $teacherId]);
        $locationId = $teacherLocation->location_id;
		
		$lessons = $this->getLessons($teacherId);
		foreach ($lessons as &$lesson) {
			if(! empty($lesson->classroomId)) {
				$toTime = new \DateTime($lesson->date);
				$length = explode(':', $lesson->duration);
				$toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
				if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
					$title = $lesson->teacher->publicIdentity;
					$class = 'group-lesson';
					$backgroundColor = null;
					if (!empty($lesson->colorCode)) {
						$class = null;
						$backgroundColor = $lesson->colorCode;
					}
					$description = $this->renderAjax('group-lesson-description', [
						'title' => $title,
						'lesson' => $lesson,
						'view' => Lesson::CLASS_ROOM_VIEW
					]);
				} else {
					$title = $lesson->teacher->publicIdentity;
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
				$description = $this->renderAjax('private-lesson-description', [
					'title' => $title,
					'lesson' => $lesson,
					'view' => Lesson::CLASS_ROOM_VIEW
				]);


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
