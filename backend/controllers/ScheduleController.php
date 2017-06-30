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
use common\models\ClassroomUnavailability;
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

	public function actionFetchHolidayName($date)
    {
		$holiday = Holiday::findOne(['DATE(date)' => $date]);
		$holidayResource = '';
		if(!empty($holiday)) {
			$holidayResource = ' (' . $holiday->description . ')';
		}
		return $holidayResource;
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
			->andWhere(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED]])
			->between($date, $date)
			->notDeleted()
			->all();
        return $lessons;
    }

    public function actionRenderClassroomResources($date)
    {
        $date      = \DateTime::createFromFormat('Y-m-d', $date);
		$classrooms = Classroom::find()
			->andWhere(['locationId' => Yii::$app->session->get('location_id')])
			->all();
		foreach ($classrooms as $classroom) {
			$resources[] = [
				'id'    => $classroom->id,
				'title' => $classroom->name,
			];
		}
        return $resources;
    }

    public function actionRenderResources($date, $programId, $teacherId)
    {
        $locationId = Yii::$app->session->get('location_id');
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
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
        return $resources;
    }

    public function actionRenderDayEvents($date, $programId, $teacherId)
    {
        $locationId = Yii::$app->session->get('location_id');
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
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
			->andWhere([
				'locationId' => Yii::$app->session->get('location_id'),
				'day' => $date->format('N')
			])
			->one();
		foreach ($classroomUnavailabilities as $classroomUnavailability) {
			list($fromTime['hours'], $fromTime['minutes'], $fromTime['seconds']) = explode(':', $locationAvailability->fromTime);	 
			$start = $date->setTime($fromTime['hours'], $fromTime['minutes'], $fromTime['seconds']);
			$end = clone $date;
			list($toTime['hours'], $toTime['minutes'], $toTime['seconds']) = explode(':', $locationAvailability->toTime);
			$end = $end->setTime($toTime['hours'], $toTime['minutes'], $toTime['seconds']);
			$events[] = [
				'resourceId' => $classroomUnavailability->classroomId,
				'title'      => '',
				'start'      => $start->format('Y-m-d H:i:s'),
				'end'        => $end->format('Y-m-d H:i:s'),
				'rendering'  => 'background',
			];
		}
		$programId = null;
		$teacherId = null;
		$lessons = $this->getLessons($date, $programId, $teacherId);
		foreach ($lessons as &$lesson) {
			if(! empty($lesson->classroomId)) {
				$toTime = new \DateTime($lesson->date);
				$length = explode(':', $lesson->fullDuration);
				$toTime->add(new \DateInterval('PT'.$length[0].'H'.$length[1].'M'));
                                $title = $lesson->classroomTitle;
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
