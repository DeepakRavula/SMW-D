<?php

namespace backend\controllers;

use Yii;
use common\models\Student;
use common\models\Program;
use common\models\Course;
use backend\models\search\StudentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\User;
use yii\web\Response;
use common\models\CourseSchedule;
use common\models\log\StudentLog;
use common\models\discount\EnrolmentDiscount;
use common\models\TeacherAvailability;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/**
 * StudentController implements the CRUD actions for Student model.
 */
class StudentController extends \common\components\backend\BackendController
{
	public function actions()
    {
        return [
            'view' => [
                'class' => 'backend\actions\student\ViewAction',
            ],
        ];
    }
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
                'only' => ['create', 'update', 'merge', 'fetch-program-rate','validate'],
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
     * Creates a new Student model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Student();
		$loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        $model->on(Student::EVENT_AFTER_INSERT, [new StudentLog(), 'create'], ['loggedUser' => $loggedUser]);
        $request = Yii::$app->request;
        $user = $request->post('User');
        if ($model->load($request->post())) {
            $model->customer_id = $user['id'];
			$model->status = Student::STATUS_ACTIVE;
            if($model->save()) {
				$response = [
					'status' => true,
					'url' => Url::to(['/student/view', 'id' => $model->id])	
				];
			} else {
				$response =  [
					'status' => false,
					'errors' => ActiveForm::validate($model),
				];		
			}
		}
		return $response;
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
		$loggedUser = User::findOne(['id' => Yii::$app->user->id]);
		$oldAttributes = $model->getOldAttributes();
		$model->on(Student::EVENT_UPDATE, [new StudentLog(), 'edit'], ['loggedUser' => $loggedUser, 'oldAttributes' => $oldAttributes]);
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if((int)$model->status === Student::STATUS_INACTIVE) {
				return $this->redirect(['/student/index', 'StudentSearch[showAllStudents]' => false]);
			} else {
				return  [
					'status' => true,
				];
			}
        } else {
			return  [
				'status' => false,
				'errors' => ActiveForm::validate($model),
			];	
		}
    }

    public function actionEnrolment($id)
    {
        $model = $this->findModel($id);
        $session = Yii::$app->session;
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
        $request = Yii::$app->request;
        $post = $request->post();
        $courseModel = new Course();
		$courseSchedule = new CourseSchedule();
		$multipleEnrolmentDiscount = new EnrolmentDiscount();
		$paymentFrequencyDiscount = new EnrolmentDiscount();
		$courseModel->load($post);
		$courseSchedule->load($post);
		
        if (Yii::$app->request->isPost) {
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
                        $multipleEnrolmentDiscount->discountType = true;
                        $multipleEnrolmentDiscount->type = EnrolmentDiscount::TYPE_MULTIPLE_ENROLMENT;
                        $multipleEnrolmentDiscount->save();
                    }
                    if (!empty($paymentFrequencyDiscount->discount)) {
                        $paymentFrequencyDiscount->enrolmentId = $courseModel->enrolment->id;
                        $paymentFrequencyDiscount->discountType = 0;
                        $paymentFrequencyDiscount->type = EnrolmentDiscount::TYPE_PAYMENT_FREQUENCY;
                        $paymentFrequencyDiscount->save();
                    }
                }
			}
            return $this->redirect(['lesson/review', 'courseId' => $courseModel->id, 'LessonSearch[showAllReviewLessons]' => false]);
        }
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
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
        $model = Student::find()
			->notDeleted()
			->location($locationId)
			->where(['student.id' => $id])->one();
        return $model;
    }

    public function actionFetchProgramRate($duration, $id = null, $paymentFrequencyDiscount = null, $multiEnrolmentDiscount = null, $rate = null)
    {
		if($id) {
			$program     = Program::findOne(['id' => $id]);
			$getDuration = \DateTime::createFromFormat('H:i', $duration);
			$hours       = $getDuration->format('H');
			$minutes     = $getDuration->format('i');
			$unit        = (($hours * 60) + $minutes) / 60;
			$lessonDuration = $hours != 00 ? $hours . 'hr' . $minutes . 'mins' : $minutes . 'mins';
			if ($rate) {
				$ratePerLesson = $unit * $rate;
			} else {
				$rate = $program->rate;
				$ratePerLesson = $unit * $program->rate;
			}
			$ratePerMonth = $ratePerLesson * 4;
			$discount = 0.0;
			if ($multiEnrolmentDiscount) {
				$discount += $multiEnrolmentDiscount / 4;
			} else {
                            $multiEnrolmentDiscount = 0;
                        }
			if ($paymentFrequencyDiscount) {
				$discount += ($ratePerLesson - $discount) * $paymentFrequencyDiscount / 100;
			}
			$ratePerLessonWithDiscount = $ratePerLesson - $discount;
			$ratePerMonthWithDiscount = $ratePerLessonWithDiscount * 4;
			return [
				'ratePerLessonWithDiscount' => $ratePerLessonWithDiscount,
				'ratePerMonthWithDiscount' => $ratePerMonthWithDiscount,
				'ratePerLesson' => $ratePerLesson,
				'ratePerMonth' => $ratePerMonth,
				'rate' => $rate
			];
		}
    }

    public function actionMerge($id)
    {
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
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
    public function actionValidate()
    {
        $model = new Student();
        
		$request = Yii::$app->request;
        if ($model->load($request->post())) {
            return  ActiveForm::validate($model);
        }
        }
}
