<?php

namespace backend\controllers;

use Yii;
use common\models\Course;
use common\models\log\CourseLog;
use common\models\Lesson;
use common\models\Qualification;
use backend\models\search\CourseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Student;
use yii\data\ActiveDataProvider;
use yii\widgets\ActiveForm;
use backend\models\UserForm;
use yii\base\Model;
use common\models\CourseSchedule;
use yii\web\Response;
use common\models\TeacherAvailability;
use common\models\Enrolment;
use common\models\log\LogHistory;
/**
 * CourseController implements the CRUD actions for Course model.
 */
class CourseController extends \common\components\backend\BackendController
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
				'only' => ['fetch-teacher-availability', 'fetch-lessons', 'fetch-group'],
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
        $studentDataProvider = new ActiveDataProvider([
            'query' => Student::find()
                ->notDeleted()
                ->groupCourseEnrolled($id)
                ->active(),
        ]);

        $lessonDataProvider = new ActiveDataProvider([
            'query' => Lesson::find()
                ->andWhere(['courseId' => $id])
                ->andWhere(['status' => [Lesson::STATUS_COMPLETED, Lesson::STATUS_SCHEDULED,
                        Lesson::STATUS_UNSCHEDULED]])
                ->isConfirmed()
                ->notDeleted()
                ->orderBy(['lesson.date' => SORT_ASC]),
        ]);
        $logDataProvider    = new ActiveDataProvider([
            'query' => LogHistory::find()
                ->course($id)]);
        return $this->render('view',
                [
                'model' => $this->findModel($id),
                'courseId' => $id,
                'studentDataProvider' => $studentDataProvider,
                'lessonDataProvider' => $lessonDataProvider,
                'logDataProvider' => $logDataProvider,
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
	public function getCourseDate($courseScheduleModels)
	{
		$courseDates = ArrayHelper::getColumn($courseScheduleModels, function ($courseSchedule) {
    		return $courseSchedule['fromTime'];
		});
		usort($courseDates, function($a, $b) {
			$date1 = new \DateTime($a);
			$date2 = new \DateTime($b);
			return $date1 < $date2 ? -1: 1;
		});
		return $courseDates[0];
	}
    public function actionCreate()
    {
		$request = Yii::$app->request;
        $response = Yii::$app->response;
        $model = new Course();
        $courseSchedule = [new CourseSchedule()];
        $model->setScenario(Course::SCENARIO_GROUP_COURSE);
        $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
         $model->on(Course::EVENT_AFTER_INSERT, [new CourseLog(), 'create'], ['loggedUser' => $loggedUser]);
        if ($model->load($request->post())) {
			$courseScheduleModels = UserForm::createMultiple(CourseSchedule::classname());
			Model::loadMultiple($courseScheduleModels, $request->post());
			if ($request->isAjax) {
                $response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validate($model),
                        ActiveForm::validateMultiple($courseScheduleModels)
                );
            }
            $valid = $model->validate();
            $valid = (Model::validateMultiple($courseScheduleModels)) && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $model->startDate           = $this->getCourseDate($courseScheduleModels);
                    $model->lessonsPerWeekCount = count($courseScheduleModels);
                    if ($flag = $model->save(false)) {
                        foreach ($courseScheduleModels as $courseScheduleModel) {
                            $courseScheduleModel->courseId = $model->id;
                            $courseScheduleModel->duration = $model->duration;
                            $dayList                       = Course::getWeekdaysList();
                            $courseScheduleModel->day      = array_search($courseScheduleModel->day,
                                $dayList);
                            if (!($flag                          = $courseScheduleModel->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
				$model->createLessons();
            			$model->trigger(Course::EVENT_CREATE);
            			return $this->redirect(['lesson/review', 'courseId' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        } else {
            return $this->render('create', [
                'model' => $model,
				'courseSchedule' => (empty($courseSchedule)) ? [new CourseSchedule] : $courseSchedule
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
                    ->andWhere(['ul.location_id' => \Yii::$app->session->get('location_id')])
                    ->notDeleted()
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
        $location_id = \Yii::$app->session->get('location_id');
        $programId = $_POST['depdrop_parents'][0];
        $qualifications = Qualification::find()
			->joinWith(['teacher' => function ($query) use ($location_id) {
				$query->joinWith(['userLocation' => function ($query) use ($location_id) {
                     $query->join('LEFT JOIN', 'user_profile','user_profile.user_id = user_location.user_id')
					->joinWith('teacherAvailability')
				->where(['location_id' => $location_id]);
				}]);
			}])
			->where(['program_id' => $programId])
                        ->notDeleted()
			->orderBy(['user_profile.firstname' => SORT_ASC])
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
	public function actionFetchGroup($studentId, $courseName = null)
	{
		$locationId = \Yii::$app->session->get('location_id');
		$groupEnrolments = Enrolment::find()
			->select(['courseId'])
			->joinWith(['course' => function ($query) use ($locationId) {
				$query->groupProgram($locationId);
			}])
			->where(['enrolment.studentId' => $studentId])
			->isConfirmed();
        $groupCourses = Course::find()
			->joinWith(['program' => function ($query) {
				$query->group();
			}])
			->where(['NOT IN', 'course.id', $groupEnrolments])
			->andWhere(['locationId' => $locationId])
		   ->andWhere(['>=', 'DATE(course.endDate)', (new \DateTime())->format('Y-m-d')])
			->confirmed();
			if(!empty($courseName)) {
				$groupCourses->andWhere(['LIKE', 'program.name', $courseName]);
			}
        $groupDataProvider = new ActiveDataProvider([
            'query' => $groupCourses,
        ]);

		$data = $this->renderAjax('/student/enrolment/_form-group', [
			'groupDataProvider' => $groupDataProvider,
			'student' => Student::findOne(['id' => $studentId])
		]);
		return [
			'status' => true,
			'data' => $data
		];
	}
}