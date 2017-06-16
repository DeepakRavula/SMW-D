<?php

namespace backend\controllers;

use Yii;
use common\models\Student;
use common\models\Enrolment;
use common\models\Lesson;
use common\models\Program;
use common\models\Course;
use backend\models\search\StudentSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\TeacherAvailability;
use common\models\ExamResult;
use common\models\Note;
use common\models\StudentLog;
use common\models\User;
use yii\helpers\Url;
use common\models\PaymentFrequency;

/**
 * StudentController implements the CRUD actions for Student model.
 */
class StudentController extends Controller
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
				'only' => ['create', 'update'],
				'formats' => [
					'application/json' => Response::FORMAT_JSON,
				],
        	],
        ];
    }

    /**
     * Lists all Student models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Student model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $locationId = Yii::$app->session->get('location_id');
        $enrolments = Enrolment::find()
			->joinWith(['course' => function($query) {
				$query->isConfirmed();
			}])
			->location($locationId)
			->notDeleted()
			->andWhere(['studentId' => $model->id]);

        $enrolmentDataProvider = new ActiveDataProvider([
            'query' => $enrolments,
        ]);

        $currentDate = new \DateTime();
        $lessons = Lesson::find()
			->studentEnrolment($locationId, $model->id)
            ->where(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED, Lesson::STATUS_MISSED]])
            ->orderBy(['lesson.date' => SORT_ASC])
            ->notDeleted();

        $lessonDataProvider = new ActiveDataProvider([
            'query' => $lessons,
        ]);

        $unscheduledLessons = Lesson::find()
			->studentEnrolment($locationId, $model->id)
            ->joinWith(['privateLesson'])
            ->andWhere(['NOT', ['private_lesson.lessonId' => null]])
            ->orderBy(['private_lesson.expiryDate' => SORT_DESC])
			->unscheduled()
			->notRescheduled()
            ->notDeleted();

        $unscheduledLessonDataProvider = new ActiveDataProvider([
            'query' => $unscheduledLessons,
        ]);    

		$examResults = ExamResult::find()
			->where(['studentId' => $model->id]);
		
        $examResultDataProvider = new ActiveDataProvider([
            'query' => $examResults,
        ]);

		$notes = Note::find()
			->where(['instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_STUDENT])
			->orderBy(['createdOn' => SORT_DESC]);

        $noteDataProvider = new ActiveDataProvider([
            'query' => $notes,
        ]);

		return $this->render('view', [
			'model' => $model,
			'lessonDataProvider' => $lessonDataProvider,
			'enrolmentDataProvider' => $enrolmentDataProvider,
			'unscheduledLessonDataProvider' => $unscheduledLessonDataProvider,
			'examResultDataProvider' => $examResultDataProvider,
			'noteDataProvider' => $noteDataProvider
		]);
    }

    /**
     * Creates a new Student model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Student();
		$userModel = User::findOne(['id' => Yii::$app->user->id]);
        $model->on(Student::EVENT_CREATE, [new StudentLog(), 'create']);
		$model->userName = $userModel->publicIdentity;
        $request = Yii::$app->request;
        $user = $request->post('User');
        if ($model->load($request->post())) {
            $model->customer_id = $user['id'];
            if($model->save()) {
				return $this->redirect(['/student/view', 'id' => $model->id]);
			}
        }
    }

    /**
     * Updates an existing Student model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$model->on(Student::EVENT_UPDATE, [new StudentLog(), 'edit']);
		$userModel = User::findOne(['id' => Yii::$app->user->id]);
		$model->userName = $userModel->publicIdentity;	
		
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return  [
				'status' => true,
				'data' => $this->renderAjax('_profile', [
					'model' => $model,
					])
			];
        }
    }

    public function actionEnrolment($id)
    {
        $model = $this->findModel($id);
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $request = Yii::$app->request;
        $post = $request->post();
        $courseModel = new Course();
        if ($courseModel->load($post)) {
            $dayList = TeacherAvailability::getWeekdaysList();
            $courseModel->locationId = $locationId;
            $courseModel->studentId = $model->id;
            $courseModel->day = array_search($courseModel->day, $dayList);
            $courseModel->save();

            return $this->redirect(['lesson/review', 'courseId' => $courseModel->id, 'LessonSearch[showAllReviewLessons]' => false]);
        }
        if (!empty($post['courseId'])) {
            $enrolmentModel = new Enrolment();
            $enrolmentModel->courseId = current($post['courseId']);
            $enrolmentModel->studentId = $model->id;
            $enrolmentModel->paymentFrequencyId = PaymentFrequency::LENGTH_FULL;
            $enrolmentModel->save();

            return $this->redirect(['lesson/group-enrolment-review', 'courseId' => $enrolmentModel->courseId, 'enrolmentId' => $enrolmentModel->id, 'LessonSearch[showAllReviewLessons]' => false]);
        }

        $groupEnrolments = Enrolment::find()
                ->select(['courseId'])
                ->joinWith(['course' => function ($query) use ($locationId) {
                    $query->groupProgram($locationId);
                }])
                ->where(['enrolment.studentId' => $model->id])
				->isConfirmed();
        $groupCourses = Course::find()
                ->joinWith(['program' => function ($query) {
                    $query->group();
                }])
                ->where(['NOT IN', 'course.id', $groupEnrolments])
                ->andWhere(['locationId' => $locationId])
				->confirmed();
        $groupCourseDataProvider = new ActiveDataProvider([
            'query' => $groupCourses,
        ]);

        return $this->render('_course', [
            'model' => $model,
            'groupCourseDataProvider' => $groupCourseDataProvider,
        ]);
    }

    /**
     * Finds the Student model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Student the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $session = Yii::$app->session;
        $locationId = $session->get('location_id');
        $model = Student::find()->location($locationId)
                ->where(['student.id' => $id])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionFetchProgramRate($id)
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $program = Program::findOne(['id' => $id]);

        return $program->rate;
    }
}
