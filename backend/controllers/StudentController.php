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
use common\models\CourseSchedule;
use common\models\ExamResult;
use common\models\Note;
use common\models\StudentLog;
use common\models\User;
use common\models\EnrolmentDiscount;
use common\models\PaymentFrequency;
use common\models\TeacherAvailability;

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
				'only' => ['create', 'update', 'merge'],
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
        $query = Enrolment::find()
			->joinWith(['course' => function($query) {
				$query->isConfirmed();
			}])
			->location($locationId)
			->notDeleted()
                        ->isConfirmed()
                        ->andWhere(['studentId' => $model->id]);
        $enrolments = $query->all();
        $allEnrolments = [];
        foreach ($enrolments as $enrolment) {
            $allEnrolments[] = [
                'teacherId' => $enrolment->course->teacherId,
                'programId' => $enrolment->course->programId
            ];
        }
        $enrolmentDataProvider = new ActiveDataProvider([
            'query' => $query->isRegular(),
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
                        'allEnrolments' => $allEnrolments,
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
			$model->status = Student::STATUS_ACTIVE;
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
			if((int)$model->status === Student::STATUS_INACTIVE) {
				return $this->redirect(['/student/index', 'StudentSearch[showAllStudents]' => false]);
			} else {
				return  [
					'status' => true,
				];
			}
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
		$courseSchedule = new CourseSchedule();
                $multipleEnrolmentDiscount = new EnrolmentDiscount();
                $paymentFrequencyDiscount = new EnrolmentDiscount();
		$courseModel->load($post);
		$courseSchedule->load($post);
		
        if (Yii::$app->request->isPost && empty($post['courseId'])) {
            $paymentFrequencyDiscount->load($post['PaymentFrequencyDiscount'], '');
            $multipleEnrolmentDiscount->load($post['MultipleEnrolmentDiscount'], '');
            $courseModel->locationId = $locationId;
			if($courseModel->save()) {
				$courseSchedule->courseId = $courseModel->id;
            	$courseSchedule->studentId = $model->id;
				$dayList = TeacherAvailability::getWeekdaysList();
		   		$courseSchedule->day = array_search($courseSchedule->day, $dayList); 
            	$courseSchedule->save();
                if ($courseSchedule->save()) {
                    if (!empty($multipleEnrolmentDiscount->discount)) {
                        $multipleEnrolmentDiscount->enrolmentId = $courseModel->enrolment->id;
                        $multipleEnrolmentDiscount->discountType = 0;
                        $multipleEnrolmentDiscount->type = EnrolmentDiscount::TYPE_MULTIPLE_ENROLMENT;
                        $multipleEnrolmentDiscount->save();
                    }
                    if (!empty($paymentFrequencyDiscount->discount)) {
                        $paymentFrequencyDiscount->enrolmentId = $courseModel->enrolment->id;
                        $paymentFrequencyDiscount->discountType = true;
                        $paymentFrequencyDiscount->type = EnrolmentDiscount::TYPE_PAYMENT_FREQUENCY;
                        $paymentFrequencyDiscount->save();
                    }
                }
			}
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

        return $this->render('/student/enrolment/view', [
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
        $model = Student::find()
                ->notDeleted()
                ->location($locationId)
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

    public function actionMerge($id)
    {
        $locationId = Yii::$app->session->get('location_id');
        $model      = Student::findOne($id);
        $model->setScenario(Student::SCENARIO_MERGE);
        $students   = Student::find()
                        ->active()
                        ->notDeleted()
                        ->customer($model->customer_id)
                        ->location($locationId)
                        ->andWhere(['NOT', ['student.id' => $id]])
                        ->all();
        $data       = $this->renderAjax('_merge', [
            'students' => $students,
            'model' => $model,
        ]);
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if ($model->validate()) {
                $studentModel = Student::findOne($model->studentId);
                foreach ($studentModel->enrolment as $enrolment) {
                    $enrolment->studentId = $model->id;
                    $enrolment->save(false);
                }
                foreach ($studentModel->notes as $note) {
                    $note->instanceId = $model->id;
                    $note->save(false);
                }
                foreach ($studentModel->logs as $log) {
                    $log->studentId = $model->id;
                    $log->save(false);
                }
                foreach ($studentModel->examResults as $examResult) {
                    $examResult->studentId = $model->id;
                    $examResult->save(false);
                }
                $studentModel->delete();
                return [
                    'status' => true,
                    'message' => 'Student successfully merged!'
                ];
            }
        } else {
            return [
                'status' => true,
                'data' => $data
            ];
        }
    }
	public function actionPrint()
    {
        $searchModel = new StudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination = false;
        
        $this->layout = '/print';

        return $this->render('_print', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
