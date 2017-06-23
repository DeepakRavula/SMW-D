<?php

namespace backend\controllers;

use Yii;
use common\models\Course;
use common\models\timelineEvent\CourseLog;
use common\models\Lesson;
use common\models\Qualification;
use backend\models\search\CourseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Student;
use common\models\Enrolment;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use common\models\CourseGroup;
use common\models\CourseSchedule;
use yii\web\Response;
use common\models\TeacherAvailability;
use common\models\PaymentFrequency;
/**
 * CourseController implements the CRUD actions for Course model.
 */
class CourseController extends Controller
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
			[
				'class' => 'yii\filters\ContentNegotiator',
				'only' => ['fetch-teacher-availability', 'fetch-lessons'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
        	],
        ];
    }

    /**
     * Lists all Course models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CourseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Course model.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;
        $enrolment = $request->post('Enrolment');
        $studentIds = $enrolment['studentIds'];
        if (!empty($studentIds)) {
            Enrolment::deleteAll(['courseId' => $id]);
            foreach ($studentIds as $studentId) {
                $enrolment = new Enrolment();
                $enrolment->setAttributes([
                    'courseId' => $id,
                    'studentId' => $studentId,
                    'isDeleted' => false,
                    'isConfirmed' => true,
                    'paymentFrequencyId' => PaymentFrequency::LENGTH_FULL,
                ]);
                $enrolment->save();
            }
        }

        $studentDataProvider = new ActiveDataProvider([
            'query' => Student::find()
                ->groupCourseEnrolled($id)
				->active(),
        ]);

		$lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
				->andWhere(['courseId' => $id])
				->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_SCHEDULED, Lesson::STATUS_UNSCHEDULED]])
				->notDeleted()
				->orderBy(['lesson.date' => SORT_ASC]),
        ]);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'courseId' => $id,
            'studentDataProvider' => $studentDataProvider,
            'lessonDataProvider' => $lessonDataProvider,
        ]);
    }
public function getHolidayEvent($date)
    {
        $locationId = Yii::$app->session->get('location_id');
        $events     = [];
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
        return $events;
    }
    public function actionViewStudent($groupCourseId, $studentId)
    {
        $model = $this->findModel($groupCourseId);
        $studentModel = Student::findOne(['id' => $studentId]);

        return $this->render('view_student', [
            'model' => $model,
            'studentModel' => $studentModel,
        ]);
    }

	public function actionFetchTeacherAvailability($teacherId)
    {
		$query = TeacherAvailability::find()
                ->joinWith('userLocation')
                ->where(['user_id' => $teacherId]);
        $teacherDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		$data = $this->renderAjax('_teacher-availability', [
        	'teacherDataProvider' => $teacherDataProvider,
    	]);

        return $data;
    }
    /**
     * Creates a new Course model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
		$post = Yii::$app->request->post();
        $model = new Course();
        $courseSchedule = new CourseSchedule();
        $model->setScenario(Course::SCENARIO_GROUP_COURSE);
		
        $model->locationId = Yii::$app->session->get('location_id');
        $userModel = User::findOne(['id' => Yii::$app->user->id]);
        $model->on(Course::EVENT_CREATE, [new CourseLog(), 'create']);
        $model->userName = $userModel->publicIdentity;
		$model->load($post);
		$courseSchedule->load($post);	
        if (Yii::$app->request->isPost) {
			if($model->save()) {
				$limit = CourseGroup::LESSONS_PER_WEEK_COUNT_ONE;
				if((int)$model->courseGroup->lessonsPerWeekCount === CourseGroup::LESSONS_PER_WEEK_COUNT_TWO) {
					$limit = CourseGroup::LESSONS_PER_WEEK_COUNT_TWO;
				}
				for ($i = 0; $i < $limit; $i++) {
        			$courseScheduleModel = new CourseSchedule();
					$courseScheduleModel->courseId = $model->id;
					$courseScheduleModel->day = $courseSchedule->day[$i];
					$courseScheduleModel->duration = $courseSchedule->duration[$i];
					$courseScheduleModel->fromTime = $courseSchedule->fromTime[$i];
					$courseScheduleModel->save();
				}
				$model->createLessons();	
            	$model->trigger(Course::EVENT_CREATE);
			}
            return $this->redirect(['lesson/review', 'courseId' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
				'courseSchedule' => $courseSchedule
            ]);
        }
    }
    /**
     * Updates an existing Course model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $teacherModel = ArrayHelper::map(User::find()
                    ->joinWith('userLocation ul')
                    ->join('INNER JOIN', 'rbac_auth_assignment raa', 'raa.user_id = user.id')
                    ->where(['raa.item_name' => 'teacher'])
                    ->andWhere(['ul.location_id' => Yii::$app->session->get('location_id')])
                    ->all(),
                'id', 'userProfile.fullName'
            );
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'teacher' => $teacherModel,
            ]);
        }
    }
public function getHolidayResources($date)
    {
        $locationId = Yii::$app->session->get('location_id');
        $resources  = [];
        $holiday    = Holiday::find()
            ->andWhere(['holiday.date' => $date->format('Y-m-d 00:00:00')])
            ->one();
        if (!empty($holiday)) {
            $resources[] = [
                'id'    => '0',
                'title' => 'Holiday',
            ];
        }
        return $resources;
    }

	    public function actionRenderResources($date)
    {
        $locationId = Yii::$app->session->get('location_id');
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
        $resources  = $this->getHolidayResources($date);
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
public function getLessons($date)
    {
        $lessons = Lesson::find()
                ->joinWith(['course' => function ($query) {
                    $query->andWhere(['course.locationId' => Yii::$app->session->get('location_id')]);
                }])
                ->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]])
                ->between($date, $date)
                ->notDeleted()
                ->all();
        return $lessons;
    }

	public function actionRenderDayEvents($date)
    {
        $locationId = Yii::$app->session->get('location_id');
        $date       = \DateTime::createFromFormat('Y-m-d', $date);
        $events     = $this->getHolidayEvent($date);
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
            }
            $lessons = $this->getLessons($date);
            foreach ($lessons as &$lesson) {
                $toTime = new \DateTime($lesson->date);
                $length = explode(':', $lesson->fullDuration);
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
			
        return $events;
    }
    /**
     * Deletes an existing Course model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Course model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $id
     *
     * @return Course the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Course::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

  public function actionTeachers()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $session = Yii::$app->session;
        $location_id = $session->get('location_id');
        $programId = $_POST['depdrop_parents'][0];
        $qualifications = Qualification::find()
			->joinWith(['teacher' => function ($query) use ($location_id) {
				$query->joinWith(['userLocation' => function ($query) use ($location_id) {
					$query->joinWith('teacherAvailability')
				->where(['location_id' => $location_id]);
				}]);
			}])
			->where(['program_id' => $programId])
			->all();
        $result = [];
        $output = [];
        foreach ($qualifications as  $qualification) {
            $output[] = [
                'id' => $qualification->teacher->id,
                'name' => $qualification->teacher->publicIdentity,
            ];
        }
        $result = [
            'output' => $output,
            'selected' => '',
        ];

        return $result;
    }

    public function actionPrint($id)
    {
        $model = $this->findModel($id);
       	$lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
				->andWhere([
					'courseId' => $model->id,
					'status' => Lesson::STATUS_SCHEDULED
				])
				->notDeleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
				'pagination' => false,
       	]);

        $this->layout = '/print';

        return $this->render('_print', [
                    'model' => $model,
                    'lessonDataProvider' => $lessonDataProvider,
        ]);
    }
}
