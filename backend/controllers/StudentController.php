<?php
namespace backend\controllers;
use Yii;
use common\models\Student;
use common\models\Program;
use common\models\Course;
use backend\models\search\StudentSearch;
use common\components\controllers\BaseController;
use common\models\Location;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\User;
use yii\web\Response;
use common\models\CourseSchedule;
use common\models\log\StudentLog;
use backend\models\discount\MultiEnrolmentDiscount;
use backend\models\discount\PaymentFrequencyEnrolmentDiscount;
use backend\models\EnrolmentForm;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\filters\AccessControl;
use common\models\Enrolment;

/**
 * StudentController implements the CRUD actions for Student model.
 */
class StudentController extends BaseController
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
                'only' => [
                    'create', 'update', 'delete', 'merge', 'fetch-program-rate', 'validate', 'fetch-rate'
                ],
                'formats' => [
                        'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'delete', 'merge', 'fetch-program-rate',
                            'create-enrolment', 'validate', 'print', 'view', 'fetch-rate'],
                        'roles' => ['manageStudents'],
                    ],
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
    public function actionCreate($userId=null)
    {
        $model = new Student();
        $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        $model->on(Student::EVENT_AFTER_INSERT, [new StudentLog(), 'create'], ['loggedUser' => $loggedUser]);
        $request = Yii::$app->request;
        $user = $request->post('User');
        $userModel = User::findOne(['id' => $userId]);
        $data = $this->renderAjax('/user/customer/_form-student', [
            'model' => $model,
	        'customer' => $userModel, 
        ]);
        if ($model->load($request->post())) {
            $model->customer_id = $user['id'];
            $model->status = Student::STATUS_INACTIVE;
            if ($model->save()) {
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
        } else {
            $response =  [
                'status' => true,
                'data' =>	$data,
            ];	
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
        $model->on(Student::EVENT_AFTER_UPDATE, [new StudentLog(), 'edit'], ['loggedUser' => $loggedUser, 'oldAttributes' => $oldAttributes]);
        $userModel = $model->customer;
        $data = $this->renderAjax('_form', [
            'model' => $model,
	        'customer' => $userModel,
        ]);
        if (Yii::$app->request->post()){
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                if ((int)$model->status === Student::STATUS_INACTIVE) {
                    return $this->redirect(['/student/index', 'StudentSearch[showAllStudents]' => false]);
                }
                $response = [
                    'status' => true
                ];
            } else {
                $response = [
                    'status' => false,
                    'errors' => ActiveForm::validate($model)
                ];
            }
        } else {
            $response = [
                'status' => true,
                'data' => $data
            ];
        }
        return $response;
    } 
    
    public function actionCreateEnrolment($id)
    {
        $courseDetail = new EnrolmentForm();
        $courseDetail->load(Yii::$app->request->get());
        $courseModel = new Course();
        $courseModel->studentId = $id;
        $courseSchedule = new CourseSchedule();
        $courseSchedule->studentId = $id;
        $multipleEnrolmentDiscount = new MultiEnrolmentDiscount();
        $paymentFrequencyDiscount = new PaymentFrequencyEnrolmentDiscount();
        $courseModel->setModel($courseDetail);
        $courseSchedule->setModel($courseDetail);
        if ($courseModel->save()) {
            $courseSchedule->isAutoRenew = $courseDetail->autoRenew;
            $courseSchedule->courseId = $courseModel->id;
            $courseSchedule->startDate = $courseModel->startDate;
            $courseSchedule->endDate = $courseModel->endDate->format('Y-m-d H:i:s');
            $courseSchedule->teacherId = $courseModel->teacherId;
            if ($courseSchedule->save()) {
                if (!empty($courseDetail->enrolmentDiscount)) {
                    $multipleEnrolmentDiscount->discount = $courseDetail->enrolmentDiscount;
                    $multipleEnrolmentDiscount->enrolmentId = $courseModel->enrolment->id;
                    $multipleEnrolmentDiscount->save();
                }
                if (!empty($courseDetail->pfDiscount)) {
                    $paymentFrequencyDiscount->discount = $courseDetail->pfDiscount;
                    $paymentFrequencyDiscount->enrolmentId = $courseModel->enrolment->id;
                    $paymentFrequencyDiscount->save();
                }
            }
        }
        return $this->redirect(['lesson/review', 'LessonReview[courseId]' => $courseModel->id, 'LessonReview[EnrolmentType]' => $courseDetail->isReverse ? Enrolment::TYPE_REVERSE : null, 
            'LessonSearch[showAllReviewLessons]' => false]);
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
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model = Student::find()
            ->notDeleted()
            ->location($locationId)
            ->andWhere(['student.id' => $id])
            ->one();
        return $model;
    }

    public function actionFetchProgramRate($duration, $id = null, $paymentFrequencyDiscount = null, $multiEnrolmentDiscount = null, $rate = null, $customerDiscount = null)
    {
        if ($id) {
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
			if($customerDiscount) {
                $discount += ($ratePerLesson - $discount) * $customerDiscount / 100;
			}
            if ($paymentFrequencyDiscount) {
                $discount += ($ratePerLesson - $discount) * $paymentFrequencyDiscount / 100;
            }
            $ratePerLessonWithDiscount = $ratePerLesson - $discount;
            $ratePerMonthWithDiscount = $ratePerLessonWithDiscount * 4;
            return [
                'ratePerLessonWithDiscount' => round($ratePerLessonWithDiscount, 2),
                'ratePerMonthWithDiscount' => round($ratePerMonthWithDiscount, 2),
                'ratePerLesson' => $ratePerLesson,
                'ratePerMonth' => $ratePerMonth,
                'rate' => $rate
            ];
        }
    }

    public function actionFetchRate($id) {
        $program     = Program::findOne(['id' => $id]);
        return [
            'rate' => $program->rate
        ];
    }

    public function actionMerge($id)
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $model      = Student::findOne($id);
        $loggedUser = User::findOne(['id' => Yii::$app->user->id]);
        $model->on(Student::EVENT_MERGE, [new StudentLog(), 'merge'], ['loggedUser' => $loggedUser]);
        $model->setScenario(Student::SCENARIO_MERGE);
        $students   = Student::find()
            ->statusActive()
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
                foreach ($studentModel->enrolments as $enrolment) {
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
                $model->trigger(Student::EVENT_MERGE);
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

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->hasEnrolment() && !$model->hasLesson()) {
            $model->delete();
            $message = null;
            $response = [
                'status' => true,
                'url' => Url::to(['student/index', 'StudentSearch[showAllStudents]' => false]),
                'message' => $message
            ];
        } else {
            $response = [
                'status' => false,
            ];
        }
        return $response;
    }
}